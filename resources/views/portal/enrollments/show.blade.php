@extends('portal.layouts.app')
@section('title', 'Enrollment Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Enrollment Details</h5>
    <a href="{{ route('portal.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Status-based notices --}}
@if($enrollment->status === 'pending')
    <div class="alert alert-warning">
        <p class="fw-semibold mb-1">
            <i class="bi bi-hourglass-split me-2"></i>
            Your enrollment is <strong>pending review</strong> by our staff.
        </p>
        <p class="mb-1 small">
            Once your application is reviewed and approved, you will need to visit
            <strong>M.B. Therapy Center</strong> in person for over-the-counter payment
            to complete the enrollment process.
        </p>
        <p class="mb-0 small text-muted">
            <i class="bi bi-chat-dots me-1"></i>
            Our facilitator will reach out to you via <strong>Messenger</strong>
            to inform you of the result and guide you through the next steps.
        </p>
    </div>

@elseif($enrollment->status === 'pending_payment')
    <div class="alert alert-info border-start border-4 border-info">
        <p class="fw-semibold mb-1">
            <i class="bi bi-check-circle-fill me-2"></i>
            Your enrollment has been <strong>approved!</strong>
        </p>
        <p class="mb-1">
            Please visit <strong>M.B. Therapy Center</strong> for
            <strong>over-the-counter payment</strong> to officially complete
            your child's enrollment.
        </p>
        <p class="mb-0 small text-muted">
            <i class="bi bi-chat-dots me-1"></i>
            Our facilitator will contact you via <strong>Messenger</strong>
            for the payment details, schedule, and further instructions.
            Please watch out for our message.
        </p>
    </div>

@elseif($enrollment->status === 'enrolled')
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Your child is now <strong>officially enrolled — payment confirmed</strong>.
        Welcome to M.B. Therapy Center!
    </div>

@elseif($enrollment->status === 'payment_confirmed')
    <div class="alert alert-primary">
        <i class="bi bi-check2-circle me-2"></i>
        Your payment has been <strong>confirmed</strong>.
        Your child will be officially enrolled shortly.
    </div>

@elseif($enrollment->status === 'rejected')
    <div class="alert alert-danger">
        <i class="bi bi-x-circle me-2"></i>
        Your enrollment was <strong>rejected</strong>.
        @if($enrollment->rejection_reason)
            Reason: <strong>{{ $enrollment->rejection_reason }}</strong>
        @endif
    </div>

@elseif($enrollment->status === 'withdrawn')
    <div class="alert alert-secondary">
        <i class="bi bi-clipboard-x me-2"></i>
        This enrollment has been <strong>withdrawn</strong>.
    </div>
@endif

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-clipboard-check me-1"></i>Enrollment Summary
            </div>
            <div class="card-body">
                @if($enrollment->student)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @include('partials.avatar',[
                            'name'  => $enrollment->student->full_name,
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
                @endif
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">School Year</td>
                        <td>{{ optional($enrollment->schoolYear)->year_label }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Level</td>
                        <td>{{ optional($enrollment->programLevel)->program_name }}</td>
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
                        <td class="text-muted">Date Filed</td>
                        <td>{{ $enrollment->enrollment_date?->format('m/d/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waiver</td>
                        <td>
                            @if($enrollment->waiver_signed)
                                <span class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>Signed
                                </span>
                            @else
                                <span class="text-danger">
                                    <i class="bi bi-x-circle me-1"></i>Not signed
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-file-earmark-check me-1"></i>Submitted Documents
            </div>
            <div class="card-body p-0">
                @forelse($enrollment->documents as $doc)
                    <div class="d-flex justify-content-between align-items-center
                                px-3 py-2 border-bottom">
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
                @empty
                    <p class="text-muted small px-3 py-2 mb-0">
                        No documents on record.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection