<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\DocumentType;
use App\Models\AuditLog;

class PaymentController extends Controller
{
    public function create(int $enrollmentId)
    {
        $enrollment = Enrollment::with([
            'student',
            'schoolYear',
            'programLevel',
            'documents.documentType',
            'payment',
        ])->findOrFail($enrollmentId);

        // Already paid
        if ($enrollment->payment) {
            return redirect()->route('admin.enrollments.show', $enrollmentId)
                ->with('error', 'A payment has already been recorded for this enrollment.');
        }

        // Must be pending_payment
        if ($enrollment->status !== 'pending_payment') {
            return redirect()->route('admin.enrollments.show', $enrollmentId)
                ->with('error', 'Payment can only be recorded when the enrollment is in Pending Payment status.');
        }

        // Walk-in gate: all required docs must be submitted
        if ($enrollment->enrollment_type === 'walk_in') {
            $blockingDocs = $this->getBlockingDocuments($enrollment);
            if (!empty($blockingDocs)) {
                return redirect()->route('admin.enrollments.show', $enrollmentId)
                    ->with('error',
                        'Cannot record payment — required document(s) not yet submitted: ' .
                        implode(', ', $blockingDocs) .
                        '. Please update the document statuses first.'
                    );
            }
        }

        return view('admin.payments.create', compact('enrollment'));
    }

    public function store(Request $request, int $enrollmentId)
    {
        $enrollment = Enrollment::with([
            'documents.documentType',
            'payment',
        ])->findOrFail($enrollmentId);

        // Guards
        if ($enrollment->payment) {
            return redirect()->route('admin.enrollments.show', $enrollmentId)
                ->with('error', 'A payment has already been recorded for this enrollment.');
        }

        if ($enrollment->status !== 'pending_payment') {
            return redirect()->route('admin.enrollments.show', $enrollmentId)
                ->with('error', 'Payment can only be recorded when the enrollment is in Pending Payment status.');
        }

        if ($enrollment->enrollment_type === 'walk_in') {
            $blockingDocs = $this->getBlockingDocuments($enrollment);
            if (!empty($blockingDocs)) {
                return redirect()->route('admin.enrollments.show', $enrollmentId)
                    ->with('error',
                        'Cannot record payment — required document(s) not yet submitted: ' .
                        implode(', ', $blockingDocs)
                    );
            }
        }

        $request->validate([
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $payment = Payment::create([
            'enrollment_id'  => $enrollment->enrollment_id,
            'amount'         => $request->amount,
            'payment_date'   => $request->payment_date,
            'payment_method' => 'cash',
            'or_number'      => null,
            'notes'          => $request->notes,
            'recorded_by'    => Auth::user()->user_id,
        ]);

        // Auto-move enrollment to Enrolled — Payment Confirmed
        $enrollment->update([
            'status'       => 'enrolled',
            'processed_by' => Auth::user()->user_id,
        ]);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'CREATE',
            'table_name' => 'payment',
            'record_id'  => $payment->payment_id,
            'changes'    => json_encode([
                'enrollment_id' => $enrollment->enrollment_id,
                'amount'        => $request->amount,
            ]),
        ]);

        Log::info('Payment recorded', [
            'by'            => Auth::user()->username,
            'enrollment_id' => $enrollment->enrollment_id,
        ]);

        return redirect()->route('admin.enrollments.show', $enrollmentId)
            ->with('success',
                'Payment recorded successfully. Enrollment status updated to Enrolled — Payment Confirmed.'
            );
    }

    // -------------------------------------------------------------------------
    // Helper — returns names of active required docs not yet submitted
    // -------------------------------------------------------------------------

    private function getBlockingDocuments(Enrollment $enrollment): array
    {
        $requiredTypes = DocumentType::where('is_required', true)
            ->where('is_active', 1)
            ->get();

        $blocking = [];
        foreach ($requiredTypes as $docType) {
            $doc = $enrollment->documents
                ->where('document_type_id', $docType->document_type_id)
                ->first();
            if (!$doc || $doc->submission_status !== 'submitted') {
                $blocking[] = $docType->document_name;
            }
        }

        return $blocking;
    }
}