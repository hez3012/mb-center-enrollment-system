<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use App\Models\EnrollmentDocument;
use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\DocumentType;
use App\Models\ProgramLevel;
use App\Models\Disability;
use App\Models\DevelopmentalPediatrician;
use App\Models\AuditLog;

class EnrollmentController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $guardian   = $user->guardian;
        $studentIds = $guardian?->students->pluck('student_id')->toArray() ?? [];

        $enrollments = Enrollment::with(['student', 'schoolYear', 'programLevel'])
            ->whereIn('student_id', $studentIds)
            ->orderByDesc('created_at')
            ->get();

        $currentYear         = SchoolYear::current();
        $hasEligibleStudents = $guardian !== null && $currentYear !== null;

        return view('portal.enrollments.index', compact(
            'enrollments',
            'hasEligibleStudents'
        ));
    }

    public function create()
    {
        $user     = Auth::user();
        $guardian = $user->guardian;

        if (!$guardian) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Your guardian profile is not set up yet.');
        }

        $currentYear   = SchoolYear::current();

        // Filter out inactive document types (e.g. Parent/Guardian Waiver)
        $documentTypes = DocumentType::where('is_active', 1)->get();

        $programLevels = ProgramLevel::all();
        $disabilities  = Disability::whereNull('deleted_at')->get();
        $devPeds       = DevelopmentalPediatrician::whereNull('deleted_at')->get();

        return view('portal.enrollments.create', compact(
            'currentYear',
            'documentTypes',
            'programLevels',
            'disabilities',
            'devPeds'
        ));
    }

    public function store(Request $request)
    {
        $user     = Auth::user();
        $guardian = $user->guardian;

        if (!$guardian) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Your guardian profile is not set up yet.');
        }

        $request->validate([
            // Student personal info
            'first_name'       => 'required|string|min:2|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'birthdate'        => 'required|date',
            'sex'              => 'required|in:male,female,others,prefer_not_to_say',
            'sex_specify'      => 'nullable|string|max:100',
            // Student address
            'region'           => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'barangay'         => 'required|string|min:4|max:100',
            'house_unit_no'    => 'required|string|min:1|max:100',
            'street'           => 'required|string|min:4|max:100',
            'zip_code'         => ['required', 'regex:/^\d{4}$/'],
            // School info
            'program_level_id' => 'required|exists:program_level,program_level_id',
            'dev_ped_id'       => 'nullable|exists:developmental_pediatrician,dev_ped_id',
            'disabilities'     => 'required|array|min:1',
            'disability_other' => 'nullable|string|max:255',
            // Enrollment
            'school_year_id'   => 'required|exists:school_year,school_year_id',
            'waiver_signed'    => 'accepted',
        ]);

        // Pre-validate file types before creating any records
        $documentTypes = DocumentType::all();
        $allowedMimes  = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

        foreach ($documentTypes as $docType) {
            $key = "doc_file.{$docType->document_type_id}";
            if ($request->hasFile($key)) {
                $mime = strtolower($request->file($key)->getMimeType() ?? '');
                if (!in_array($mime, $allowedMimes)) {
                    return back()
                        ->with('error', "Invalid file type for \"{$docType->document_name}\". Only JPG, PNG, or PDF are accepted.")
                        ->withInput();
                }
            }
        }

        // Create the student record linked to this guardian
        $student = Student::create([
            'first_name'       => $request->first_name,
            'middle_name'      => $request->middle_name,
            'last_name'        => $request->last_name,
            'birthdate'        => $request->birthdate,
            'sex'              => $request->sex,
            'sex_specify'      => $request->sex === 'others' ? $request->sex_specify : null,
            'region'           => $request->region,
            'province'         => $request->province,
            'city'             => $request->city,
            'barangay'         => $request->barangay,
            'house_unit_no'    => $request->house_unit_no,
            'street'           => $request->street,
            'zip_code'         => $request->zip_code,
            'program_level_id' => $request->program_level_id,
            'dev_ped_id'       => $request->dev_ped_id,
            'guardian_id'      => $guardian->guardian_id,
            'status'           => 'active',
            'disability_other' => $request->disability_other,
        ]);

        $student->disabilities()->sync($request->input('disabilities', []));

        // Create enrollment
        $enrollment = Enrollment::create([
            'student_id'       => $student->student_id,
            'school_year_id'   => $request->school_year_id,
            'program_level_id' => $student->program_level_id,
            'enrollment_date'  => now()->toDateString(),
            'enrollment_type'  => 'online',
            'status'           => 'pending',
            'waiver_signed'    => true,
            'processed_by'     => null,
        ]);

        // Store document files
        foreach ($documentTypes as $docType) {
            $filePath         = null;
            $submissionStatus = 'pending';
            $key              = "doc_file.{$docType->document_type_id}";

            if ($request->hasFile($key)) {
                $filePath = $request->file($key)
                    ->store("enrollment_docs/{$enrollment->enrollment_id}", 'public');
                $submissionStatus = 'submitted';
            }

            EnrollmentDocument::create([
                'enrollment_id'     => $enrollment->enrollment_id,
                'document_type_id'  => $docType->document_type_id,
                'submission_status' => $submissionStatus,
                'file_path'         => $filePath,
                'submission_date'   => $filePath ? now()->toDateString() : null,
            ]);
        }

        AuditLog::create([
            'user_id'    => $user->user_id,
            'action'     => 'CREATE',
            'table_name' => 'enrollment',
            'record_id'  => $enrollment->enrollment_id,
            'changes'    => json_encode([
                'type'    => 'online',
                'student' => $student->full_name,
            ]),
        ]);

        return redirect()->route('portal.enrollments.show', $enrollment->enrollment_id)
            ->with('success', 'Your enrollment has been submitted successfully! Please wait for our internals to review your application.');
    }

    public function show($id)
    {
        $user       = Auth::user();
        $guardian   = $user->guardian;
        $studentIds = $guardian?->students->pluck('student_id')->toArray() ?? [];

        $enrollment = Enrollment::with([
            'student',
            'schoolYear',
            'programLevel',
            'documents.documentType',
        ])->findOrFail($id);

        if (!in_array($enrollment->student_id, $studentIds)) {
            abort(403);
        }

        return view('portal.enrollments.show', compact('enrollment'));
    }
}
