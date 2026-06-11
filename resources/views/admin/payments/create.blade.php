@extends('admin.layouts.app')
@section('title', 'Record Payment')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Record Payment</h5>
    <a href="{{ route('admin.enrollments.show', $enrollment->enrollment_id) }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Enrollment
    </a>
</div>

{{-- Enrollment Summary --}}
<div class="alert alert-light border mb-4">
    <div class="row g-2">
        <div class="col-md-3">
            <small class="text-muted d-block">Student</small>
            <strong>{{ optional($enrollment->student)->list_name }}</strong>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">School Year</small>
            <strong>{{ optional($enrollment->schoolYear)->year_label }}</strong>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Program Level</small>
            <strong>{{ optional($enrollment->programLevel)->program_name }}</strong>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Enrollment Type</small>
            <span class="badge bg-{{ $enrollment->enrollment_type === 'walk_in' ? 'secondary' : 'info text-dark' }}">
                {{ $enrollment->type_label }}
            </span>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST"
              action="{{ route('admin.enrollments.payment.store', $enrollment->enrollment_id) }}">
            @csrf

            <p class="fw-semibold text-primary small mb-3">
                <i class="bi bi-cash-coin me-1"></i>Payment Details
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Amount Paid (₱) <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="amount" step="0.01" min="1"
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}"
                           placeholder="0.00"
                           required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Payment Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="payment_date"
                           class="form-control @error('payment_date') is-invalid @enderror"
                           value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                           required>
                    @error('payment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Payment Method</label>
                    <input type="hidden" name="payment_method" value="cash">
                    <div class="form-control bg-light">
                        <i class="bi bi-cash me-1 text-success"></i>
                        <strong>Cash</strong>
                        <span class="text-muted small ms-1">(over-the-counter)</span>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">
                        Notes
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="notes"
                           class="form-control @error('notes') is-invalid @enderror"
                           value="{{ old('notes') }}"
                           placeholder="e.g. Full payment received at front desk">
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-warning mt-4 py-2">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <strong>Important:</strong> Recording this payment will automatically
                update the enrollment status to
                <strong>Enrolled — Payment Confirmed</strong>.
                This action cannot be undone without manually editing the enrollment.
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-cash-coin me-1"></i>Confirm & Record Payment
            </button>
        </form>
    </div>
</div>
@endsection