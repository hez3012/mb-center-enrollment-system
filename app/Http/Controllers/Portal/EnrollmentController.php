<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Enrollment;
use App\Models\EnrollmentDocument;
use App\Models\SchoolYear;
use App\Models\DocumentType;
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

        // Check if guardian has any students still eligible to enroll
        $currentYear = SchoolYear::current();
        $enrolledIds = [];
        if ($currentYear) {
            $enrolledIds = Enrollment::where('school_year_id', $currentYear->school_year_id)
                ->whereNotIn('status', ['rejected', 'withdrawn'])
                ->whereNull('deleted_at')
                ->pluck('student_id')
                ->toArray();
        }

        $hasEligibleStudents = $guardian
            ? $guardian->students()->where('status', 'active')
            ->whereNotIn('student_id', $enrolledIds)
            ->exists()
            : false;

        return view('portal.enrollments.index', compact('enrollments', 'hasEligibleStudents'));
    }

    public function create()
    {
        $user     = Auth::user();
        $guardian = $user->guardian;

        if (!$guardian) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Your guardian profile is not set up yet.');
        }

        $students      = $guardian->students()->where('status', 'active')->get();
        $currentYear   = SchoolYear::current();
        $documentTypes = DocumentType::all();

        // Exclude students already enrolled this school year
        if ($currentYear) {
            $enrolledIds = Enrollment::where('school_year_id', $currentYear->school_year_id)
                ->whereNotIn('status', ['rejected', 'withdrawn'])
                ->whereNull('deleted_at')
                ->pluck('student_id')
                ->toArray();
            $students = $students->whereNotIn('student_id', $enrolledIds);
        }

        return view('portal.enrollments.create', compact(
            'students',
            'currentYear',
            'documentTypes'
        ));
    }

    public function store(Request $request)
    {
        $user     = Auth::user();
        $guardian = $user->guardian;

        // Validate non-file fields only
        $request->validate([
            'student_id'     => 'required|exists:student,student_id',
            'school_year_id' => 'required|exists:school_year,school_year_id',
            'waiver_signed'  => 'accepted',
        ]);

        // Ensure student belongs to this guardian
        $studentBelongs = $guardian?->students->contains('student_id', $request->student_id);
        if (!$studentBelongs) {
            return back()->with('error', 'Invalid student selection.')->withInput();
        }

        // Get program level from student record
        $student = \App\Models\Student::find($request->student_id);
        if (!$student || !$student->program_level_id) {
            return back()
                ->with('error', 'This student does not have a program level assigned. Please contact the administrator.')
                ->withInput();
        }

        // Check duplicate enrollment
        $duplicate = Enrollment::where('student_id', $request->student_id)
            ->where('school_year_id', $request->school_year_id)
            ->whereNotIn('status', ['rejected', 'withdrawn'])
            ->whereNull('deleted_at')
            ->exists();

        if ($duplicate) {
            return back()
                ->with('error', 'This student is already enrolled for the selected school year.')
                ->withInput();
        }

        // Pre-validate file types BEFORE creating any records
        $documentTypes = \App\Models\DocumentType::all();
        $allowedMimes  = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

        foreach ($documentTypes as $docType) {
            $key = "doc_file.{$docType->document_type_id}";
            if ($request->hasFile($key)) {
                $detectedMime = strtolower($request->file($key)->getMimeType() ?? '');
                if (!in_array($detectedMime, $allowedMimes)) {
                    return back()
                        ->with('error', "Invalid file type for \"{$docType->document_name}\". Only JPG, PNG, or PDF files are accepted.")
                        ->withInput();
                }
            }
        }

        // Create enrollment
        $enrollment = Enrollment::create([
            'student_id'       => $request->student_id,
            'school_year_id'   => $request->school_year_id,
            'program_level_id' => $student->program_level_id,
            'enrollment_date'  => now()->toDateString(),
            'enrollment_type'  => 'online',
            'status'           => 'pending',
            'waiver_signed'    => true,
            'processed_by'     => null,
        ]);

        // Store files
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
            'changes'    => json_encode(['type' => 'online', 'student_id' => $request->student_id]),
        ]);

        return redirect()->route('portal.enrollments.show', $enrollment->enrollment_id)
            ->with('success', 'Enrollment submitted successfully. Please wait for admin approval.');
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
            'documents.documentType'
        ])->findOrFail($id);

        if (!in_array($enrollment->student_id, $studentIds)) {
            abort(403);
        }

        return view('portal.enrollments.show', compact('enrollment'));
    }
}
