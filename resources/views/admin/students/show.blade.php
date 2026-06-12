@extends('admin.layouts.app')
@section('title', 'Student Details')
@section('content')

@php
    $isSpED = str_contains($student->serviceType?->service_name ?? '', 'SpED');
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Student Details</h5>
    @if($fromGuardian)
        <a href="{{ route('admin.guardians.show', ['id' => $fromGuardian]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Guardian
        </a>
    @else
        <a href="{{ route('admin.students.index') }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-3">
    {{-- LEFT --}}
    <div class="col-md-7">

        {{-- Student Information --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Student Information
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    @include('partials.avatar', [
                        'name'  => $student->full_name,
                        'image' => $student->profile_picture,
                        'size'  => 72,
                    ])
                    <div>
                        <div class="fw-bold fs-5">{{ $student->full_name }}</div>
                        <small class="text-muted">
                            {{ $student->age }} years old
                        </small>
                    </div>
                </div>
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Status</td>
                        <td>
                            @php
                                $sc = [
                                    'active'    => 'success',
                                    'inactive'  => 'secondary',
                                    'withdrawn' => 'warning',
                                    'completed' => 'primary',
                                ];
                            @endphp
                            <span class="badge bg-{{ $sc[$student->status] ?? 'secondary' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sex</td>
                        <td>
                            {{ ucfirst(str_replace('_', ' ', $student->sex)) }}
                            @if($student->sex === 'others' && $student->sex_specify)
                                <span class="text-muted small">
                                    ({{ $student->sex_specify }})
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ $student->birthdate?->format('F j, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ $student->full_address ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Service & Disability --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-heart-pulse me-1"></i>Service & Disability
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Service Type</td>
                        <td>
                            <strong>
                                {{ $student->serviceType?->service_name ?? '—' }}
                            </strong>
                        </td>
                    </tr>
                    @if($isSpED)
                        <tr>
                            <td class="text-muted">Program Level</td>
                            <td>{{ $student->programLevel?->program_name ?? '—' }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Disability / Condition</td>
                        <td>
                            {{ $student->disability?->disability_name ?? '—' }}
                            @if($student->disability?->disability_name === 'Others'
                                && $student->disability_other)
                                <span class="text-muted small">
                                    — {{ $student->disability_other }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="col-md-5">

        {{-- Guardian --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-heart me-1"></i>Guardian Information
            </div>
            <div class="card-body">
                @if($student->guardian?->user)
                    @php $gu = $student->guardian->user; @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @include('partials.avatar', [
                            'name'  => $gu->full_name,
                            'image' => $gu->profile_picture ?? null,
                            'size'  => 48,
                        ])
                        <div>
                            <div class="fw-semibold">{{ $gu->full_name }}</div>
                            <small class="text-muted">
                                {{ ucfirst($student->guardian->relationship ?? '—') }}
                            </small>
                        </div>
                    </div>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Email</td>
                            <td>{{ $gu->email ?? '—' }}</td>
                        </tr>
                        @if(!empty($gu->contact_number))
                            <tr>
                                <td class="text-muted">Contact</td>
                                <td>{{ $gu->contact_number }}</td>
                            </tr>
                        @endif
                        @if($gu->full_address)
                            <tr>
                                <td class="text-muted">Address</td>
                                <td>{{ $gu->full_address }}</td>
                            </tr>
                        @endif
                    </table>
                @else
                    <p class="text-muted small mb-0">No guardian linked.</p>
                @endif
            </div>
        </div>

        {{-- Developmental Pediatrician --}}
        @if($student->developmentalPediatrician)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-hospital me-1"></i>Developmental Pediatrician
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Name</td>
                            <td>{{ $student->developmentalPediatrician->name ?? '—' }}</td>
                        </tr>
                    </table>
                    @if($student->dev_ped_document)
                        <a href="{{ Storage::url($student->dev_ped_document) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="bi bi-file-earmark me-1"></i>
                            View Dev. Ped. Document
                        </a>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
@endsection