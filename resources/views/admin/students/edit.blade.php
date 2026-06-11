@extends('admin.layouts.app')
@section('title', 'Edit Student')
@section('content')

@php
$statusDesc = [
    'active'    => 'Currently enrolled and actively attending the program.',
    'inactive'  => 'Enrolled but not currently attending.',
    'withdrawn' => 'Has withdrawn from the program.',
    'completed' => 'Has successfully completed the program.',
];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Edit Student — {{ $student->list_name }}</h5>
    <a href="{{ route('admin.students.show',$student->student_id) }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST"
              action="{{ route('admin.students.update',$student->student_id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Profile Picture --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person-circle me-1"></i>Profile Picture
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div id="avatarWrapper" style="cursor:pointer;">
                            @include('partials.avatar',[
                                'name'  => $student->list_name,
                                'image' => $student->profile_picture,
                                'size'  => 56,
                            ])
                        </div>
                        <span class="text-muted small">Current picture</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <label for="profilePicInput"
                               class="btn btn-sm btn-outline-secondary mb-0">
                            <i class="bi bi-image me-1"></i>Choose Picture
                        </label>
                        <input type="file" name="profile_picture" id="profilePicInput"
                               class="d-none @error('profile_picture') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png">
                        <span id="picFileName" class="text-muted small">No file chosen</span>
                    </div>
                    @error('profile_picture')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG or PNG only · Max 50MB · Optional</small>
                </div>
            </div>

            {{-- Personal Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person me-1"></i>Personal Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name',$student->first_name) }}"
                           required minlength="2">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name',$student->middle_name) }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name',$student->last_name) }}"
                           required minlength="2">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light"
                           readonly value="{{ $student->middle_initial }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="birthdate" id="birthdateInput"
                           class="form-control @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate',$student->birthdate?->format('Y-m-d')) }}"
                           required>
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" id="ageDisplay" class="form-control bg-light"
                           readonly
                           value="{{ $student->age !== null ? $student->age.' years old' : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Sex <span class="text-danger">*</span>
                    </label>
                    <select name="sex" id="sexSelect"
                            class="form-select @error('sex') is-invalid @enderror" required>
                        @foreach(['male'=>'Male','female'=>'Female','prefer_not_to_say'=>'Prefer not to say','others'=>'Others'] as $val => $label)
                            <option value="{{ $val }}"
                                    {{ old('sex',$student->sex) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 {{ old('sex',$student->sex) === 'others' ? '' : 'd-none' }}"
                     id="sexSpecifyWrapper">
                    <label class="form-label fw-semibold">Please specify</label>
                    <input type="text" name="sex_specify"
                           class="form-control @error('sex_specify') is-invalid @enderror"
                           value="{{ old('sex_specify',$student->sex_specify) }}">
                    @error('sex_specify')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Address --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Address
            </p>
            <div class="mb-4">
                @include('partials.address-fields',[
                    'fieldPrefix' => '',
                    'data' => [
                        'region'        => old('region',        $student->region        ?? ''),
                        'province'      => old('province',      $student->province      ?? ''),
                        'city'          => old('city',          $student->city          ?? ''),
                        'barangay'      => old('barangay',      $student->barangay      ?? ''),
                        'house_unit_no' => old('house_unit_no', $student->house_unit_no ?? ''),
                        'street'        => old('street',        $student->street        ?? ''),
                        'zip_code'      => old('zip_code',      $student->zip_code      ?? ''),
                    ],
                ])
            </div>

            {{-- School Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-mortarboard me-1"></i>School Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Guardian <span class="text-danger">*</span>
                    </label>
                    <select name="guardian_id"
                            class="form-select @error('guardian_id') is-invalid @enderror" required>
                        <option value="">-- Select Guardian --</option>
                        @foreach($guardians as $guardian)
                            <option value="{{ $guardian->guardian_id }}"
                                    {{ old('guardian_id',$student->guardian_id) == $guardian->guardian_id ? 'selected' : '' }}>
                                {{ optional($guardian->user)->list_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Program Level <span class="text-danger">*</span>
                    </label>
                    <select name="program_level_id" id="programLevelSelect"
                            class="form-select @error('program_level_id') is-invalid @enderror"
                            required>
                        <option value="">-- Select Program --</option>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    data-desc="{{ $level->description }}"
                                    {{ old('program_level_id',$student->program_level_id) == $level->program_level_id ? 'selected' : '' }}>
                                {{ $level->program_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_level_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted" id="programLevelDesc">
                        Select a program to see its description.
                    </small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Developmental Pediatrician
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <select name="dev_ped_id"
                            class="form-select @error('dev_ped_id') is-invalid @enderror">
                        <option value="">-- Select --</option>
                        @foreach($devPeds as $ped)
                            <option value="{{ $ped->dev_ped_id }}"
                                    {{ old('dev_ped_id',$student->dev_ped_id) == $ped->dev_ped_id ? 'selected' : '' }}>
                                {{ $ped->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('dev_ped_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select name="status" id="statusSelect"
                            class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['active'=>'Active','inactive'=>'Inactive','withdrawn'=>'Withdrawn','completed'=>'Completed'] as $val => $label)
                            <option value="{{ $val }}"
                                    {{ old('status',$student->status) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted" id="statusDesc">
                        {{ $statusDesc[old('status',$student->status)] ?? '' }}
                    </small>
                </div>

                {{-- Disabilities --}}
                <div class="col-md-12">
                    <label class="form-label fw-semibold">
                        Disabilities <span class="text-danger">*</span>
                        <span class="text-muted small fw-normal">(select all that apply)</span>
                    </label>
                    @error('disabilities')
                        <div class="text-danger small mb-1">{{ $message }}</div>
                    @enderror
                    <div class="border rounded p-3 bg-light">
                        <div class="row g-2">
                            @foreach($disabilities as $disability)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="disabilities[]"
                                               value="{{ $disability->disability_id }}"
                                               id="dis_{{ $disability->disability_id }}"
                                               {{ $student->disabilities->contains('disability_id',$disability->disability_id) ? 'checked' : '' }}>
                                        <label class="form-check-label small"
                                               for="dis_{{ $disability->disability_id }}">
                                            {{ $disability->disability_name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="disOthersCheck"
                                           {{ old('disability_other',$student->disability_other) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="disOthersCheck">
                                        Others (please specify)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 {{ old('disability_other',$student->disability_other) ? '' : 'd-none' }}"
                                 id="disOthersWrapper">
                                <input type="text" name="disability_other"
                                       class="form-control form-control-sm @error('disability_other') is-invalid @enderror"
                                       placeholder="Please specify other disability"
                                       value="{{ old('disability_other',$student->disability_other) }}">
                                @error('disability_other')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            At least one disability must be selected.
                        </small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
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
var statusDescriptions = {
    'active':    'Currently enrolled and actively attending the program.',
    'inactive':  'Enrolled but not currently attending.',
    'withdrawn': 'Has withdrawn from the program.',
    'completed': 'Has successfully completed the program.'
};

document.getElementById('programLevelSelect').addEventListener('change', function() {
    var desc = this.options[this.selectedIndex]?.dataset.desc || '';
    document.getElementById('programLevelDesc').textContent = desc;
});

document.getElementById('statusSelect').addEventListener('change', function() {
    document.getElementById('statusDesc').textContent = statusDescriptions[this.value] || '';
});

document.getElementById('sexSelect').addEventListener('change', function() {
    document.getElementById('sexSpecifyWrapper')
        .classList.toggle('d-none', this.value !== 'others');
});

document.getElementById('middleNameInput').addEventListener('input', function() {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

document.getElementById('birthdateInput').addEventListener('change', function() {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    var birth = new Date(this.value);
    var today = new Date();
    var age   = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) { age--; }
    document.getElementById('ageDisplay').value = age + ' years old';
});

document.getElementById('disOthersCheck').addEventListener('change', function() {
    document.getElementById('disOthersWrapper')
        .classList.toggle('d-none', !this.checked);
    if (!this.checked) {
        document.querySelector('input[name="disability_other"]').value = '';
    }
});

document.getElementById('profilePicInput').addEventListener('change', function() {
    var file = this.files[0];
    if (!file) return;
    document.getElementById('picFileName').textContent = file.name;
    var reader = new FileReader();
    reader.onload = function(e) {
        var w = document.getElementById('avatarWrapper');
        w.innerHTML = '<img src="' + e.target.result + '" style="width:56px;height:56px;border-radius:50%;object-fit:cover;cursor:pointer;flex-shrink:0;">';
        w.querySelector('img').addEventListener('click', function() {
            document.getElementById('fullscreenImg').src = this.src;
            new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
        });
    };
    reader.readAsDataURL(file);
});

(function() {
    var w   = document.getElementById('avatarWrapper');
    var img = w ? w.querySelector('img') : null;
    if (img) {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            document.getElementById('fullscreenImg').src = this.src;
            new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
        });
    }
    var progSel = document.getElementById('programLevelSelect');
    if (progSel && progSel.value) {
        document.getElementById('programLevelDesc').textContent =
            progSel.options[progSel.selectedIndex]?.dataset.desc || '';
    }
})();
</script>
@endsection