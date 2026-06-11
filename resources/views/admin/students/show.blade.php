@extends('admin.layouts.app')
@section('title', 'Student Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Student Details</h5>
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@php
    $na = 'N/A';
    $statusColors = [
        'active'    => 'success',
        'inactive'  => 'secondary',
        'withdrawn' => 'warning',
        'completed' => 'info',
    ];
@endphp

<div class="row g-3">
    {{-- Left Panel --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <div id="avatarWrapper" style="display:inline-block;cursor:pointer;">
                    @include('partials.avatar',[
                        'name'  => $student->list_name,
                        'image' => $student->profile_picture,
                        'size'  => 80,
                    ])
                </div>
                <div class="fw-bold mt-3">{{ $student->list_name }}</div>
                <span class="badge bg-{{ $statusColors[$student->status] ?? 'secondary' }} mt-1">
                    {{ ucfirst($student->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="col-md-8 d-flex flex-column gap-3">

        {{-- Personal Information --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Personal Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">Full Name</td>
                        <td>{{ $student->full_name ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sex</td>
                        <td>{{ $student->sex_display ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ $student->birthdate?->format('m/d/Y') ?? $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Age</td>
                        <td>
                            {{ $student->age !== null
                                ? $student->age . ' years old'
                                : $na }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ $student->full_address ?: $na }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- School Information --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-mortarboard me-1"></i>School Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">Guardian</td>
                        <td>
                            {{ optional($student->guardian?->user)->full_name ?? $na }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Relationship</td>
                        <td>{{ $student->guardian?->relationship ?? $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Level</td>
                        <td>
                            {{ optional($student->programLevel)->program_name ?? $na }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>{{ ucfirst($student->status) ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dev. Pediatrician</td>
                        <td>{{ optional($student->devPed)->name ?? $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dev. Ped. Document</td>
                        <td>{{ $student->dev_ped_document ?: $na }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Disabilities --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-heart-pulse me-1"></i>Disabilities
            </div>
            <div class="card-body">
                @if($student->disabilities->count() > 0 || $student->disability_other)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($student->disabilities as $disability)
                            <span class="badge bg-light text-dark border">
                                {{ $disability->disability_name }}
                            </span>
                        @endforeach
                        @if($student->disability_other)
                            <span class="badge bg-light text-dark border">
                                {{ $student->disability_other }}
                            </span>
                        @endif
                    </div>
                @else
                    <span class="text-muted small">{{ $na }}</span>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- Fullscreen Modal --}}
<div class="modal fade" id="fullscreenModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 p-1 justify-content-end">
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="fullscreenImg" src="" alt=""
                     class="img-fluid rounded" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var w   = document.getElementById('avatarWrapper');
    var img = w ? w.querySelector('img') : null;
    if (img) {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function () {
            document.getElementById('fullscreenImg').src = this.src;
            new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
        });
    }
}());
</script>
@endsection