@extends('portal.layouts.app')
@section('title', 'My Enrollments')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">My Enrollments</h5>
    @if($enrollments->isNotEmpty() && $hasEligibleStudents)
    <a href="{{ route('portal.enrollments.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Submit New Enrollment
    </a>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($enrollments->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-x text-muted d-block mb-3" style="font-size:2.5rem;"></i>
        <p class="fw-semibold mb-1">No enrollment records yet.</p>
        @if($hasEligibleStudents)
        <p class="text-muted small mb-4">
            Submit an enrollment request for your child to get started.
        </p>
        <a href="{{ route('portal.enrollments.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Submit New Enrollment
        </a>
        @else
        <p class="text-muted small mb-0">
            All linked students are already enrolled, or no active students are
            linked to your account. Please contact the administrator for assistance.
        </p>
        @endif
    </div>
</div>
@else
@php
$activeStatuses = ['pending','pending_payment','payment_confirmed','enrolled'];
$activeGroup = $enrollments->filter(fn($e) => in_array($e->status, $activeStatuses));
$withdrawnGroup = $enrollments->filter(fn($e) => $e->status === 'withdrawn');
$rejectedGroup = $enrollments->filter(fn($e) => $e->status === 'rejected');

$sections = [];
if ($activeGroup->isNotEmpty()) {
$sections[] = ['label'=>'Active Enrollments','icon'=>'bi-clipboard-check','color'=>'text-primary','items'=>$activeGroup];
}
if ($withdrawnGroup->isNotEmpty()) {
$sections[] = ['label'=>'Withdrawn','icon'=>'bi-clipboard-x','color'=>'text-secondary','items'=>$withdrawnGroup];
}
if ($rejectedGroup->isNotEmpty()) {
$sections[] = ['label'=>'Rejected','icon'=>'bi-x-circle','color'=>'text-danger','items'=>$rejectedGroup];
}
@endphp

@foreach($sections as $section)
<p class="fw-semibold small {{ $section['color'] }} mb-2 mt-3">
    <i class="bi {{ $section['icon'] }} me-1"></i>{{ $section['label'] }}
</p>
<div class="row g-3 mb-2">
    @foreach($section['items'] as $enrollment)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @include('partials.avatar',[
                        'name' => optional($enrollment->student)->full_name ?? '?',
                        'image' => optional($enrollment->student)->profile_picture ?? null,
                        'size' => 36,
                        ])
                        <div>
                            <div class="fw-semibold">
                                {{ optional($enrollment->student)->full_name }}
                            </div>
                            <div class="text-muted small">
                                {{ optional($enrollment->schoolYear)->year_label }}
                            </div>
                        </div>
                    </div>
                    <span class="badge bg-{{ $enrollment->status_badge }}">
                        {{ $enrollment->status_label }}
                    </span>
                </div>
                <table class="table table-sm mb-2">
                    <tr>
                        <td class="text-muted small" style="width:40%">Service / Program</td>
                        <td class="small">
                            @if(optional($enrollment->programLevel)->program_name)
                            {{ $enrollment->programLevel->program_name }}
                            @elseif($enrollment->student?->serviceType?->service_name)
                            {{ $enrollment->student->serviceType->service_name }}
                            @else
                            —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted small">Type</td>
                        <td class="small">{{ $enrollment->type_label }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small">Date Filed</td>
                        <td class="small">
                            {{ $enrollment->enrollment_date
                                            ? $enrollment->enrollment_date->format('m/d/Y')
                                            : '—' }}
                        </td>
                    </tr>
                </table>
                @if($enrollment->status === 'rejected' && $enrollment->rejection_reason)
                <div class="alert alert-danger py-1 px-2 small mb-2">
                    <i class="bi bi-x-circle me-1"></i>
                    {{ $enrollment->rejection_reason }}
                </div>
                @endif
                <a href="{{ route('portal.enrollments.show',$enrollment->enrollment_id) }}"
                    class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-eye me-1"></i>View Details
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endforeach
@endif
@endsection