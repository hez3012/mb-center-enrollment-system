<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\EnrollmentDocument;
use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\ProgramLevel;
use App\Models\DocumentType;
use App\Models\ServiceType;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    public function index(Request $request)
    {
        $query = Enrollment::with([
            'student.serviceType',
            'student.guardian.user',
            'schoolYear',
            'programLevel',
        ])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student', function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name',  'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('school_year')) {
            $query->where('school_year_id', $request->school_year);
        }

        $enrollments = $query->paginate(15)->withQueryString();
        $schoolYears = SchoolYear::all();

        return view('admin.enrollments.index', compact('enrollments', 'schoolYears'));
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function create()
    {
        $currentYear = SchoolYear::current();

        if (!$currentYear) {
            return redirect()->route('admin.enrollments.index')
                ->with('error', 'No active school year found.');
        }

        $enrolledStudentIds = Enrollment::where('school_year_id', $currentYear->school_year_id)
            ->whereNotIn('status', ['withdrawn', 'rejected'])
            ->pluck('student_id')
            ->toArray();

        $students      = Student::with('serviceType')
            ->whereNotIn('student_id', $enrolledStudentIds)
            ->orderBy('last_name')
            ->get();

        $programLevels = ProgramLevel::all();
        $documentTypes = DocumentType::where('is_active', 1)->get();
        $schoolYears   = SchoolYear::all();
        $spedId        = $this->spedId();

        return view('admin.enrollments.create', compact(
            'students',
            'programLevels',
            'documentTypes',
            'schoolYears',
            'currentYear',
            'spedId'
        ));
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $student = Student::find($request->student_id);
        $isSpED  = $student && (int) $student->service_type_id === $this->spedId();

        $validated = $request->validate([
            'student_id'       => 'required|exists:student,student_id',
            'school_year_id'   => 'required|exists:school_year,school_year_id',
            'program_level_id' => $isSpED
                ? 'required|exists:program_level,program_level_id'
                : 'nullable|exists:program_level,program_level_id',
            'enrollment_date'  => 'required|date',
            'status'           => 'required|in:pending,pending_payment,enrolled,rejected,withdrawn,completed',
            'remarks'          => 'nullable|string|max:500',
            'rejection_reason' => 'nullable|string|max:500',
            'waiver_signed'    => 'nullable|boolean',
            'doc_file.*'       => 'nullable|file|max:51200',
            'doc_status.*'     => 'nullable|in:pending,submitted,missing',
            'doc_notes.*'      => 'nullable|string|max:255',
        ]);

        if (!$isSpED) {
            $validated['program_level_id'] = null;
        }

        $enrollment = Enrollment::create([
            'student_id'       => $validated['student_id'],
            'school_year_id'   => $validated['school_year_id'],
            'program_level_id' => $validated['program_level_id'] ?? null,
            'enrollment_date'  => $validated['enrollment_date'],
            'enrollment_type'  => 'walk_in',
            'status'           => $validated['status'],
            'remarks'          => $validated['remarks'] ?? null,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'waiver_signed'    => $request->boolean('waiver_signed'),
            'processed_by'     => Auth::id(),
        ]);

        $docTypes = DocumentType::where('is_active', 1)->get();
        foreach ($docTypes as $docType) {
            $id     = $docType->document_type_id;
            $status = $request->input("doc_status.{$id}", 'missing');
            $path   = null;

            if ($request->hasFile("doc_file.{$id}")) {
                $path   = $request->file("doc_file.{$id}")
                    ->store('enrollment_documents', 'public');
                $status = in_array($status, ['pending', 'submitted'])
                    ? $status : 'pending';
            }

            EnrollmentDocument::create([
                'enrollment_id'    => $enrollment->enrollment_id,
                'document_type_id' => $id,
                'file_path'        => $path,
                'submission_status' => $path ? $status : 'missing',
                'notes'            => $request->input("doc_notes.{$id}"),
            ]);
        }

        AuditLog::create([
            'action'       => 'create',
            'table_name'   => 'enrollment',
            'record_id'    => $enrollment->enrollment_id,
            'changes'      => 'Created walk-in enrollment for student ID ' . $validated['student_id'],
            'user_id'      => Auth::id(),
            'timestamp'    => now(),
        ]);

        return redirect()
            ->route('admin.enrollments.show', $enrollment->enrollment_id)
            ->with('success', 'Walk-in enrollment created successfully.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $enrollment = Enrollment::with([
            'student.guardian.user',
            'student.serviceType',
            'student.disability',
            'student.programLevel',
            'schoolYear',
            'programLevel',
            'processedBy',
            'documents.documentType',
            'payment.recordedBy',
        ])->findOrFail($id);

        $blockingDocs = $enrollment->documents->filter(function ($doc) {
            return $doc->documentType?->is_required
                && $doc->documentType?->is_active
                && $doc->submission_status !== 'submitted';
        });

        return view('admin.enrollments.show', compact('enrollment', 'blockingDocs'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        if ($enrollment->status === 'pending' && $enrollment->enrollment_type === 'online') {
            return redirect()
                ->route('admin.enrollments.show', $enrollment->enrollment_id)
                ->with(
                    'error',
                    'Use the Approve / Reject buttons for pending online enrollments.'
                );
        }

        $enrollment->load(['documents.documentType', 'payment', 'student.serviceType']);

        return view('admin.enrollments.edit', [
            'enrollment'    => $enrollment,
            'programLevels' => ProgramLevel::all(),
            'documentTypes' => DocumentType::where('is_active', 1)->get(),
            'spedId'        => $this->spedId(),
        ]);
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::with(['payment', 'documents', 'student.serviceType'])
            ->findOrFail($id);
        $hasPayment = $enrollment->payment !== null;
        $isSpED     = (int) $enrollment->student?->service_type_id === $this->spedId();

        $allowedStatuses = $hasPayment
            ? ['enrolled', 'withdrawn', 'completed']
            : ($enrollment->status === 'pending' && $enrollment->enrollment_type === 'online'
                ? ['pending']
                : ['pending', 'pending_payment', 'rejected', 'withdrawn']);

        $validated = $request->validate([
            'program_level_id' => $isSpED
                ? 'required|exists:program_level,program_level_id'
                : 'nullable|exists:program_level,program_level_id',
            'enrollment_date'  => 'required|date',
            'status'           => 'required|in:' . implode(',', $allowedStatuses),
            'remarks'          => 'nullable|string|max:500',
            'rejection_reason' => 'nullable|string|max:500',
            'waiver_signed'    => 'nullable|boolean',
            'doc_file.*'       => 'nullable|file|max:51200',
            'doc_status.*'     => 'nullable|in:pending,submitted,missing',
            'doc_notes.*'      => 'nullable|string|max:255',
        ]);

        if (!$isSpED) {
            $validated['program_level_id'] = null;
        }

        $enrollment->update([
            'program_level_id' => $validated['program_level_id'] ?? null,
            'enrollment_date'  => $validated['enrollment_date'],
            'status'           => $validated['status'],
            'remarks'          => $validated['remarks'] ?? null,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'waiver_signed'    => $request->boolean('waiver_signed'),
        ]);

        $docTypes = DocumentType::where('is_active', 1)->get();
        foreach ($docTypes as $docType) {
            $id  = $docType->document_type_id;
            $doc = $enrollment->documents
                ->where('document_type_id', $id)
                ->first();

            $status = $request->input("doc_status.{$id}");
            $path   = $doc?->file_path;

            if ($request->hasFile("doc_file.{$id}")) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
                $path   = $request->file("doc_file.{$id}")
                    ->store('enrollment_documents', 'public');
                $status = in_array($status, ['pending', 'submitted'])
                    ? $status : 'pending';
            }

            $docData = [
                'file_path'         => $path,
                'submission_status' => $path ? ($status ?? 'pending') : 'missing',
                'notes'             => $request->input("doc_notes.{$id}"),
            ];

            $doc
                ? $doc->update($docData)
                : EnrollmentDocument::create(array_merge($docData, [
                    'enrollment_id'    => $enrollment->enrollment_id,
                    'document_type_id' => $id,
                ]));
        }

        AuditLog::create([
            'action'       => 'update',
            'table_name'   => 'enrollment',
            'record_id'    => $enrollment->enrollment_id,
             'changes'      => 'Updated enrollment ID ' . $enrollment->enrollment_id,
            'user_id'      => Auth::id(),
            'timestamp'    => now(),
        ]);

        return redirect()
            ->route('admin.enrollments.show', $enrollment->enrollment_id)
            ->with('success', 'Enrollment updated successfully.');
    }

    // ── Approve ───────────────────────────────────────────────────────────────
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'Only pending enrollments can be approved.');
        }

        // For online enrollments, auto-mark all docs as submitted on approval
        if ($enrollment->enrollment_type === 'online') {
            $enrollment->documents()->update(['submission_status' => 'submitted']);
        }

        $enrollment->update([
            'status'       => 'pending_payment',
            'processed_by' => Auth::id(),
        ]);

        AuditLog::create([
            'action'       => 'approve',
            'table_name'   => 'enrollment',
            'record_id'    => $enrollment->enrollment_id,
            'changes'      => 'Approved enrollment ID ' . $enrollment->enrollment_id,
            'user_id'      => Auth::id(),
            'timestamp'    => now(),
        ]);

        return back()->with('success', 'Enrollment approved. Status set to Pending Payment.');
    }

    // ── Reject ────────────────────────────────────────────────────────────────
    public function reject(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);
        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'Only pending enrollments can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $enrollment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'processed_by'     => Auth::id(),
        ]);

        AuditLog::create([
            'action'       => 'reject',
            'table_name'   => 'enrollment',
            'record_id'    => $enrollment->enrollment_id,
            'changes'      => 'Rejected enrollment ID ' . $enrollment->enrollment_id,
            'user_id'      => Auth::id(),
            'timestamp'    => now(),
        ]);

        return back()->with('success', 'Enrollment has been rejected.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        if ($enrollment->payment) {
            return back()->with(
                'error',
                'Cannot delete an enrollment with a recorded payment.'
            );
        }

        $id = $enrollment->enrollment_id;
        $enrollment->delete();

        AuditLog::create([
            'action'       => 'delete',
            'table_name'   => 'enrollment',
            'record_id'    => $id,
            'changes'      => 'Deleted enrollment ID ' . $id,
            'user_id'      => Auth::id(),
            'timestamp'    => now(),
        ]);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted.');
    }
}
