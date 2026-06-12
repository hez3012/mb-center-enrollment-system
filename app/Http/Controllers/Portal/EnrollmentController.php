<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentDocument;
use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\ProgramLevel;
use App\Models\ServiceType;
use App\Models\Disability;
use App\Models\DocumentType;
use App\Models\DevelopmentalPediatrician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    // ── Helper ────────────────────────────────────────────────────────────────
    private function spedId(): int
    {
        $match = ServiceType::all()->first(function ($st) {
            return stripos($st->service_name, 'sped') !== false
                || stripos($st->service_name, 'special education') !== false;
        });
        return (int) $match?->service_type_id;
    }

    // ── Index ─────────────────────────────────────────────────────────────────
    public function index()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser    = Auth::user();
        $guardian    = $authUser?->guardian;
        $currentYear = SchoolYear::current();

        $enrollments = Enrollment::whereHas('student', function ($q) use ($guardian) {
            $q->where('guardian_id', $guardian?->guardian_id);
        })->with(['student.serviceType', 'schoolYear', 'programLevel'])
            ->latest()
            ->get();

        $hasEligibleStudents = $guardian && $currentYear;

        return view(
            'portal.enrollments.index',
            compact('enrollments', 'hasEligibleStudents')
        );
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function create()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser    = Auth::user();
        $guardian    = $authUser?->guardian;
        $currentYear = SchoolYear::current();

        if (!$guardian || !$currentYear) {
            return redirect()->route('portal.enrollments.index')
                ->with('error', 'Enrollment is not available at this time.');
        }

        return view('portal.enrollments.create', [
            'guardian'      => $guardian,
            'currentYear'   => $currentYear,
            'serviceTypes'  => ServiceType::all(),
            'disabilities'  => Disability::all(),
            'programLevels' => ProgramLevel::all(),
            'documentTypes' => DocumentType::where('is_active', 1)->get(),
            'spedId'        => $this->spedId(),
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser    = Auth::user();
        $guardian    = $authUser?->guardian;
        $currentYear = SchoolYear::current();

        if (!$guardian || !$currentYear) {
            return redirect()->route('portal.enrollments.index')
                ->with('error', 'Enrollment is not available at this time.');
        }

        $spedId   = $this->spedId();
        $isSpED   = (int) $request->service_type_id === $spedId;
        $isOthers = Disability::find($request->disability_id)?->disability_name === 'Others';

        $validated = $request->validate([
            'student_first_name'  => 'required|string|min:2|max:50',
            'student_last_name'   => 'required|string|min:2|max:50',
            'student_middle_name' => 'nullable|string|max:50',
            'student_birthdate'   => 'required|date|before:today',
            'student_sex'         => 'required|in:male,female,others,prefer_not_to_say',
            'student_sex_specify' => 'nullable|string|max:100',
            'house_unit_no'       => 'nullable|string|max:100',
            'street'              => 'nullable|string|min:4|max:100',
            'barangay'            => 'nullable|string|min:4|max:100',
            'city'                => 'nullable|string|max:100',
            'province'            => 'nullable|string|max:100',
            'region'              => 'nullable|string|max:100',
            'zip_code'            => ['nullable', 'regex:/^\d{4}$/'],
            'service_type_id'     => 'required|exists:service_type,service_type_id',
            'disability_id'       => 'required|exists:disability,disability_id',
            'disability_other'    => $isOthers
                ? 'required|string|max:255'
                : 'nullable|string|max:255',
            'program_level_id'    => $isSpED
                ? 'required|exists:program_level,program_level_id'
                : 'nullable|exists:program_level,program_level_id',
            'remarks'             => 'nullable|string|max:500',
            'waiver_signed'       => 'nullable|boolean',
            'doc_file.*'          => 'nullable|file|max:51200',
            'doc_notes.*'         => 'nullable|string|max:255',
        ]);

        $student = Student::create([
            'first_name'       => $validated['student_first_name'],
            'last_name'        => $validated['student_last_name'],
            'middle_name'      => $validated['student_middle_name'] ?? null,
            'birthdate'        => $validated['student_birthdate'],
            'sex'              => $validated['student_sex'],
            'sex_specify'      => $validated['student_sex_specify'] ?? null,
            'house_unit_no'    => $validated['house_unit_no'] ?? null,
            'street'           => $validated['street'] ?? null,
            'barangay'         => $validated['barangay'] ?? null,
            'city'             => $validated['city'] ?? null,
            'province'         => $validated['province'] ?? null,
            'region'           => $validated['region'] ?? null,
            'zip_code'         => $validated['zip_code'] ?? null,
            'guardian_id'      => $guardian->guardian_id,
            'service_type_id'  => $validated['service_type_id'],
            'disability_id'    => $validated['disability_id'],
            'disability_other' => $isOthers ? ($validated['disability_other'] ?? null) : null,
            'program_level_id' => $isSpED ? ($validated['program_level_id'] ?? null) : null,
            'status'           => 'active',
        ]);

        $enrollment = Enrollment::create([
            'student_id'       => $student->student_id,
            'school_year_id'   => $currentYear->school_year_id,
            'program_level_id' => $isSpED ? ($validated['program_level_id'] ?? null) : null,
            'enrollment_date'  => now()->toDateString(),
            'enrollment_type'  => 'online',
            'status'           => 'pending',
            'remarks'          => $validated['remarks'] ?? null,
            'waiver_signed'    => $request->boolean('waiver_signed'),
            'processed_by'     => null,
        ]);

        $docTypes = DocumentType::where('is_active', 1)->get();
        foreach ($docTypes as $docType) {
            $id   = $docType->document_type_id;
            $path = null;

            if ($request->hasFile("doc_file.{$id}")) {
                $path = $request->file("doc_file.{$id}")
                    ->store('enrollment_documents', 'public');
            }

            EnrollmentDocument::create([
                'enrollment_id'    => $enrollment->enrollment_id,
                'document_type_id' => $id,
                'file_path'        => $path,
                'submission_status' => $path ? 'pending' : 'missing',
                'notes'            => $request->input("doc_notes.{$id}"),
            ]);
        }

        return redirect()
            ->route('portal.enrollments.show', $enrollment->enrollment_id)
            ->with(
                'success',
                'Your enrollment has been submitted successfully! ' .
                    'Please wait for our staff to review your application.'
            );
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show($id)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $guardian = $authUser?->guardian;

        $enrollment = Enrollment::with([
            'student',
            'schoolYear',
            'programLevel',
            'documents.documentType',
        ])->findOrFail($id);

        if ($enrollment->student?->guardian_id !== $guardian?->guardian_id) {
            abort(403);
        }

        return view('portal.enrollments.show', compact('enrollment'));
    }
}
