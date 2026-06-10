@extends('portal.layouts.app')
@section('title', 'Enrollment Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Enrollment Details</h5>
    <a href="{{ route('portal.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if($enrollment->status === 'pending')
<div class="alert alert-warning">
    <i class="bi bi-hourglass-split me-2"></i>
    Your enrollment is <strong>pending review</strong> by the administrator.
</div>
@elseif($enrollment->status === 'pending_payment')
<div class="alert alert-info">
    <i class="bi bi-cash-coin me-2"></i>
    Your enrollment has been <strong>approved</strong>. Please proceed to the center to complete your payment.
</div>
@elseif($enrollment->status === 'payment_confirmed')
<div class="alert alert-primary">
    <i class="bi bi-check2-circle me-2"></i>
    Your payment has been <strong>confirmed</strong>. Your child will be officially enrolled shortly.
</div>
@elseif($enrollment->status === 'enrolled')
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>
    Your child is now <strong>officially enrolled</strong>.
</div>
@elseif($enrollment->status === 'rejected')
<div class="alert alert-danger">
    <i class="bi bi-x-circle me-2"></i>
    Your enrollment was <strong>rejected</strong>.
    @if($enrollment->rejection_reason)
        Reason: <strong>{{ $enrollment->rejection_reason }}</strong>
    @endif
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
                            <small class="text-muted">{{ $enrollment->student->age }} years old</small>
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
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
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
                            @php $dc=['submitted'=>'success','pending'=>'warning','missing'=>'danger']; @endphp
                            <span class="badge bg-{{ $dc[$doc->submission_status] ?? 'secondary' }}">
                                {{ ucfirst($doc->submission_status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small px-3 py-2 mb-0">No documents on record.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection