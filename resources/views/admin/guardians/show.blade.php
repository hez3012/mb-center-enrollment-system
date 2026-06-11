@extends('admin.layouts.app')
@section('title', 'Guardian Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Guardian Details</h5>
    <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-3">
    {{-- Left Panel --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <div id="avatarWrapper" style="display:inline-block;cursor:pointer;">
                    @include('partials.avatar',[
                        'name'  => optional($guardian->user)->list_name ?? '?',
                        'image' => optional($guardian->user)->profile_picture ?? null,
                        'size'  => 80,
                    ])
                </div>
                <div class="fw-bold mt-3">
                    {{ optional($guardian->user)->list_name ?? '—' }}
                </div>
                <span class="badge bg-secondary mt-1">Guardian</span>
                <br>
                <span class="badge bg-{{ optional($guardian->user)->is_active ? 'success' : 'secondary' }} mt-1">
                    {{ optional($guardian->user)->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Personal Information
            </div>
            <div class="card-body">
                @php $u = $guardian->user; $na = 'N/A'; @endphp
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">Full Name</td>
                        <td>{{ optional($u)->full_name ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sex</td>
                        <td>{{ optional($u)->sex_display ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ optional($u)->email ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Username</td>
                        <td>{{ optional($u)->username ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ optional($u)->birthdate?->format('m/d/Y') ?? $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Age</td>
                        <td>
                            {{ optional($u)->age !== null
                                ? optional($u)->age . ' years old'
                                : $na }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #1</td>
                        <td>{{ optional($u)->contact_number_1 ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #2</td>
                        <td>{{ optional($u)->contact_number_2 ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ optional($u)->full_address ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Relationship to Student</td>
                        <td>{{ $guardian->relationship ?: $na }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($guardian->students->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-people me-1"></i>Linked Students
                    <span class="badge bg-primary ms-1">{{ $guardian->students->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Program Level</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guardian->students as $student)
                                <tr>
                                    <td>{{ $student->list_name }}</td>
                                    <td>
                                        {{ optional($student->programLevel)->program_name ?? '—' }}
                                    </td>
                                    <td>
                                        @php
                                            $sc = ['active'=>'success','inactive'=>'secondary',
                                                   'withdrawn'=>'warning','completed'=>'info'];
                                        @endphp
                                        <span class="badge bg-{{ $sc[$student->status] ?? 'secondary' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.students.show',$student->student_id) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-3">
                    <i class="bi bi-people d-block mb-1" style="font-size:1.5rem;"></i>
                    No linked students yet.
                </div>
            </div>
        @endif
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