<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Enrollment;
use App\Models\EnrollmentDocument;
use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\ProgramLevel;
use App\Models\DocumentType;
use App\Models\AuditLog;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with([
            'student',
            'schoolYear',
            'programLevel',
            'processedBy'
        ])->orderByDesc('created_at')->get();

        $schoolYears   = SchoolYear::orderByDesc('start_date')->get();
        $programLevels = ProgramLevel::all();

        return view('admin.enrollments.index', compact(
            'enrollments',
            'schoolYears',
            'programLevels'
        ));
    }

    public function create()
    {
        $currentYear = SchoolYear::current();

        // Exclude students who already have any enrollment in the current school year
        $enrolledIds = [];
        if ($currentYear) {
            $enrolledIds = Enrollment::where('school_year_id', $currentYear->school_year_id)
                ->whereNull('deleted_at')
                ->pluck('student_id')
                ->toArray();
        }

        $students      = Student::with('programLevel')
            ->where('status', 'active')
            ->whereNotIn('student_id', $enrolledIds)
            ->get();

        $schoolYears   = SchoolYear::orderByDesc('start_date')->get();
        $programLevels = ProgramLevel::all();
        $documentTypes = DocumentType::all();

        return view('admin.enrollments.create', compact(
            'students',
            'schoolYears',
            'programLevels',
            'documentTypes',
            'currentYear'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'       => 'required|exists:student,student_id',
            'school_year_id'   => 'required|exists:school_year,school_year_id',
            'program_level_id' => 'required|exists:program_level,program_level_id',
            'enrollment_date'  => 'required|date',
            'status'           => 'required|in:pending,pending_payment,payment_confirmed,enrolled,rejected,withdrawn',
            'waiver_signed'    => 'boolean',
            'remarks'          => 'nullable|string',
            'doc_status.*'     => 'nullable|in:submitted,pending,missing',
            'doc_file.*'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_notes.*'      => 'nullable|string',
        ]);

        // Prevent duplicate enrollment for same student + school year
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

        $enrollment = Enrollment::create([
            'student_id'       => $request->student_id,
            'school_year_id'   => $request->school_year_id,
            'program_level_id' => $request->program_level_id,
            'enrollment_date'  => $request->enrollment_date,
            'enrollment_type'  => 'walk_in',
            'status'           => $request->status,
            'waiver_signed'    => $request->boolean('waiver_signed'),
            'remarks'          => $request->remarks,
            'processed_by'     => Auth::user()->user_id,
        ]);

        // Save document checklist
        if ($request->has('doc_status')) {
            foreach ($request->doc_status as $docTypeId => $status) {
                $filePath = null;
                if ($request->hasFile("doc_file.{$docTypeId}")) {
                    $filePath = $request->file("doc_file.{$docTypeId}")
                        ->store("enrollment_docs/{$enrollment->enrollment_id}", 'public');
                }

                EnrollmentDocument::create([
                    'enrollment_id'    => $enrollment->enrollment_id,
                    'document_type_id' => $docTypeId,
                    'submission_status' => $status ?? 'pending',
                    'file_path'        => $filePath,
                    'submission_date'  => ($status === 'submitted') ? now()->toDateString() : null,
                    'notes'            => $request->input("doc_notes.{$docTypeId}"),
                ]);
            }
        }

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'CREATE',
            'table_name' => 'enrollment',
            'record_id'  => $enrollment->enrollment_id,
            'changes'    => json_encode([
                'student'     => $enrollment->student->full_name ?? '',
                'school_year' => $enrollment->schoolYear->year_label ?? '',
            ]),
        ]);

        return redirect()->route('admin.enrollments.show', $enrollment->enrollment_id)
            ->with('success', 'Enrollment created successfully.');
    }

    public function show($id)
    {
        $enrollment = Enrollment::with([
            'student.guardian',
            'student.disabilities',
            'schoolYear',
            'programLevel',
            'processedBy',
            'documents.documentType',
        ])->findOrFail($id);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit($id)
    {
        $enrollment    = Enrollment::with(['documents.documentType'])->findOrFail($id);
        $schoolYears   = SchoolYear::orderByDesc('start_date')->get();
        $programLevels = ProgramLevel::all();
        $documentTypes = DocumentType::all();

        return view('admin.enrollments.edit', compact(
            'enrollment',
            'schoolYears',
            'programLevels',
            'documentTypes'
        ));
    }

    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $request->validate([
            'program_level_id' => 'required|exists:program_level,program_level_id',
            'enrollment_date'  => 'required|date',
            'status'           => 'required|in:pending,pending_payment,payment_confirmed,enrolled,rejected,withdrawn,completed',
            'waiver_signed'    => 'boolean',
            'rejection_reason' => 'nullable|string',
            'remarks'          => 'nullable|string',
            'doc_status.*'     => 'nullable|in:submitted,pending,missing',
            'doc_file.*'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_notes.*'      => 'nullable|string',
        ]);

        $enrollment->update([
            'program_level_id' => $request->program_level_id,
            'enrollment_date'  => $request->enrollment_date,
            'status'           => $request->status,
            'waiver_signed'    => $request->boolean('waiver_signed'),
            'rejection_reason' => $request->rejection_reason,
            'remarks'          => $request->remarks,
            'processed_by'     => Auth::user()->user_id,
        ]);

        // Update document statuses
        if ($request->has('doc_status')) {
            foreach ($request->doc_status as $docTypeId => $status) {
                $doc      = EnrollmentDocument::where('enrollment_id', $enrollment->enrollment_id)
                    ->where('document_type_id', $docTypeId)->first();
                $filePath = $doc?->file_path;

                if ($request->hasFile("doc_file.{$docTypeId}")) {
                    if ($filePath) Storage::disk('public')->delete($filePath);
                    $filePath = $request->file("doc_file.{$docTypeId}")
                        ->store("enrollment_docs/{$enrollment->enrollment_id}", 'public');
                }

                EnrollmentDocument::updateOrCreate(
                    ['enrollment_id' => $enrollment->enrollment_id, 'document_type_id' => $docTypeId],
                    [
                        'submission_status' => $status ?? 'pending',
                        'file_path'         => $filePath,
                        'submission_date'   => ($status === 'submitted')
                            ? ($doc?->submission_date ?? now()->toDateString())
                            : null,
                        'notes'             => $request->input("doc_notes.{$docTypeId}"),
                    ]
                );
            }
        }

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'enrollment',
            'record_id'  => $enrollment->enrollment_id,
            'changes'    => json_encode(['status' => $enrollment->status]),
        ]);

        return redirect()->route('admin.enrollments.show', $enrollment->enrollment_id)
            ->with('success', 'Enrollment updated successfully.');
    }

    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'Only pending enrollments can be approved.');
        }

        $enrollment->update([
            'status'       => 'pending_payment',
            'processed_by' => Auth::user()->user_id,
        ]);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'enrollment',
            'record_id'  => $enrollment->enrollment_id,
            'changes'    => json_encode(['action' => 'approved', 'status' => 'pending_payment']),
        ]);

        return back()->with('success', 'Enrollment approved. Student is now pending payment.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'Only pending enrollments can be rejected.');
        }

        $enrollment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'processed_by'     => Auth::user()->user_id,
        ]);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'enrollment',
            'record_id'  => $enrollment->enrollment_id,
            'changes'    => json_encode(['action' => 'rejected', 'reason' => $request->rejection_reason]),
        ]);

        return back()->with('success', 'Enrollment rejected.');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        foreach ($enrollment->documents as $doc) {
            if ($doc->file_path) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $enrollment->delete();

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'DELETE',
            'table_name' => 'enrollment',
            'record_id'  => $id,
            'changes'    => json_encode(['deleted_enrollment' => $id]),
        ]);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }
}
