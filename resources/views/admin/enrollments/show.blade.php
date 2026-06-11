@extends('admin.layouts.app')
@section('title', 'Enrollment Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Enrollment Details</h5>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Approve / Reject bar (online pending only) --}}
@if($enrollment->status === 'pending'
    && $enrollment->enrollment_type === 'online'
    && Auth::user()->hasPermission('approve_enrollment'))
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-hourglass-split me-2"></i>
            This is a <strong>pending online enrollment</strong> waiting for your review.
        </span>
        <div class="d-flex gap-2">
            <form method="POST"
                  action="{{ route('admin.enrollments.approve', $enrollment->enrollment_id) }}"
                  class="d-inline">
                @csrf
                @method('PATCH')
                <button class="btn btn-sm btn-success">
                    <i class="bi bi-check-circle me-1"></i>Approve
                </button>
            </form>
            <button class="btn btn-sm btn-danger"
                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-1"></i>Reject
            </button>
        </div>
    </div>
@endif

{{-- Payment action bar (pending_payment only) --}}
@if($enrollment->status === 'pending_payment'
    && Auth::user()->hasPermission('record_payment')
    && !$enrollment->payment)
    @if(!empty($blockingDocs))
        <div class="alert alert-warning">
            <p class="fw-semibold mb-1">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Payment cannot be recorded yet — required document(s) not submitted:
            </p>
            <ul class="mb-1 small">
                @foreach($blockingDocs as $docName)
                    <li>{{ $docName }}</li>
                @endforeach
            </ul>
            <small class="text-muted">
                Update the document statuses to <strong>Submitted</strong> via the
                <a href="{{ route('admin.enrollments.edit', $enrollment->enrollment_id) }}">
                    Edit page
                </a> first.
            </small>
        </div>
    @else
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-cash-coin me-2"></i>
                This enrollment is <strong>ready for payment</strong>.
                Record the payment to mark the student as Enrolled.
            </span>
            <a href="{{ route('admin.enrollments.payment.create', $enrollment->enrollment_id) }}"
               class="btn btn-sm btn-success">
                <i class="bi bi-cash-coin me-1"></i>Record Payment
            </a>
        </div>
    @endif
@endif

{{-- Main content --}}
<div class="row g-3">
    {{-- Left: Enrollment Information --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-clipboard-check me-1"></i>Enrollment Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Student</td>
                        <td>{{ optional($enrollment->student)->list_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">School Year</td>
                        <td>{{ optional($enrollment->schoolYear)->year_label ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Level</td>
                        <td>{{ optional($enrollment->programLevel)->program_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Enrollment Type</td>
                        <td>
                            <span class="badge bg-{{ $enrollment->enrollment_type === 'walk_in' ? 'secondary' : 'info text-dark' }}">
                                {{ $enrollment->type_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $enrollment->status_badge }}">
                                {{ $enrollment->status_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Enrollment Date</td>
                        <td>{{ $enrollment->enrollment_date?->format('m/d/Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waiver Signed</td>
                        <td>
                            @if($enrollment->waiver_signed)
                                <span class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>Yes
                                </span>
                            @else
                                <span class="text-danger">
                                    <i class="bi bi-x-circle me-1"></i>No
                                </span>
                            @endif
                        </td>
                    </tr>
                    @if($enrollment->remarks)
                        <tr>
                            <td class="text-muted">Remarks</td>
                            <td>{{ $enrollment->remarks }}</td>
                        </tr>
                    @endif
                    @if($enrollment->rejection_reason)
                        <tr>
                            <td class="text-muted">Rejection Reason</td>
                            <td class="text-danger">{{ $enrollment->rejection_reason }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Processed By</td>
                        <td>{{ optional($enrollment->processedBy)->full_name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date Created</td>
                        <td>{{ $enrollment->created_at?->format('m/d/Y h:i A') ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: Student Info + Documents --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Student Information
            </div>
            <div class="card-body">
                @if($enrollment->student)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @include('partials.avatar',[
                            'name'  => $enrollment->student->list_name,
                            'image' => $enrollment->student->profile_picture,
                            'size'  => 56,
                        ])
                        <div>
                            <div class="fw-semibold">{{ $enrollment->student->full_name }}</div>
                            <small class="text-muted">
                                {{ $enrollment->student->age }} years old
                            </small>
                        </div>
                    </div>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Guardian</td>
                            {{-- Fixed: goes through ->user->full_name --}}
                            <td>
                                {{ optional($enrollment->student->guardian?->user)->full_name ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Disabilities</td>
                            <td>
                                @forelse($enrollment->student->disabilities as $d)
                                    <span class="badge bg-info text-dark">
                                        {{ $d->disability_name }}
                                    </span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-file-earmark-check me-1"></i>Documents
            </div>
            <div class="card-body p-0">
                @forelse($enrollment->documents as $doc)
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">
                                    {{ optional($doc->documentType)->document_name }}
                                </div>
                                @if($doc->notes)
                                    <div class="text-muted small">{{ $doc->notes }}</div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($doc->file_path)
                                    <a href="{{ Storage::url($doc->file_path) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i>
                                    </a>
                                @endif
                                @php
                                    $dc = [
                                        'submitted' => 'success',
                                        'pending'   => 'warning',
                                        'missing'   => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $dc[$doc->submission_status] ?? 'secondary' }}">
                                    {{ ucfirst($doc->submission_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small px-3 py-2 mb-0">No documents on record.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Payment Details (shown after payment is recorded) --}}
@if($enrollment->payment)
    <div class="card border-0 shadow-sm mt-3 border-start border-success border-4">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="bi bi-cash-coin me-1"></i>Payment Record
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <small class="text-muted d-block">OR Number</small>
                    <strong>{{ $enrollment->payment->or_number }}</strong>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Amount Paid</small>
                    <strong class="text-success fs-6">
                        ₱{{ number_format($enrollment->payment->amount, 2) }}
                    </strong>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Payment Date</small>
                    <strong>
                        {{ $enrollment->payment->payment_date->format('m/d/Y') }}
                    </strong>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Method</small>
                    <strong>{{ $enrollment->payment->method_label }}</strong>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Recorded By</small>
                    <strong>
                        {{ optional($enrollment->payment->recordedBy)->full_name ?? '—' }}
                    </strong>
                </div>
                <div class="col-md-2">
                    <small class="text-muted d-block">Recorded On</small>
                    <strong>
                        {{ $enrollment->payment->created_at?->format('m/d/Y h:i A') ?? '—' }}
                    </strong>
                </div>
                @if($enrollment->payment->notes)
                    <div class="col-md-12">
                        <small class="text-muted d-block">Notes</small>
                        <span>{{ $enrollment->payment->notes }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Reject Modal --}}
@if($enrollment->status === 'pending')
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title text-danger fw-bold">
                        <i class="bi bi-x-circle me-1"></i>Reject Enrollment
                    </h6>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>
                <form method="POST"
                      action="{{ route('admin.enrollments.reject', $enrollment->enrollment_id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <label class="form-label fw-semibold">
                            Reason for Rejection
                        </label>
                        <textarea name="rejection_reason" rows="3"
                                  class="form-control @error('rejection_reason') is-invalid @enderror"
                                  placeholder="Explain why this enrollment is being rejected..."
                                  required>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-x-circle me-1"></i>Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection