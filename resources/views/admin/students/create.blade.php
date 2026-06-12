@extends('admin.layouts.app')
@section('title', 'Add New Student')
@section('content')

@php
    $initServiceId = (int) old('service_type_id', 0);
    $initDisabId   = (int) old('disability_id', 0);
    $initIsSpED    = $initServiceId === (int) $spedId;
    $initIsOthers  = \App\Models\Disability::find($initDisabId)?->disability_name === 'Others';
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Add New Student</h5>
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.students.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Profile Picture --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person-circle me-1"></i>Profile Picture
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div id="avatarWrapper">
                            @include('partials.avatar', ['name' => '?', 'image' => null, 'size' => 48])
                        </div>
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
                           value="{{ old('first_name') }}" required minlength="2">
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name') }}">
                    @error('middle_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required minlength="2">
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light"
                           readonly placeholder="Auto">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Sex <span class="text-danger">*</span>
                    </label>
                    <select name="sex" id="sexSelect"
                            class="form-select @error('sex') is-invalid @enderror" required>
                        <option value="">-- Select --</option>
                        @foreach(['male'=>'Male','female'=>'Female','prefer_not_to_say'=>'Prefer not to say','others'=>'Others'] as $val => $lbl)
                            <option value="{{ $val }}"
                                    {{ old('sex') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                    @error('sex')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3" id="sexSpecifyWrapper" style="display:none;">
                    <label class="form-label fw-semibold">Please Specify</label>
                    <input type="text" name="sex_specify"
                           class="form-control @error('sex_specify') is-invalid @enderror"
                           value="{{ old('sex_specify') }}">
                    @error('sex_specify')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="birthdate" id="birthdateInput"
                           class="form-control @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate') }}" required>
                    @error('birthdate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" id="ageDisplay" class="form-control bg-light"
                           readonly placeholder="Auto">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select name="status"
                            class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['active'=>'Active','inactive'=>'Inactive','withdrawn'=>'Withdrawn','completed'=>'Completed'] as $val => $lbl)
                            <option value="{{ $val }}"
                                    {{ old('status', 'active') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Address --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Address
            </p>
            <div class="row g-3 mb-4">
                @include('partials.address-fields', [
                    'data' => [
                        'region'        => old('region'),
                        'province'      => old('province'),
                        'city'          => old('city'),
                        'barangay'      => old('barangay'),
                        'street'        => old('street'),
                        'house_unit_no' => old('house_unit_no'),
                        'zip_code'      => old('zip_code'),
                    ],
                ])
            </div>

            {{-- Guardian & Dev Ped --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-people me-1"></i>Guardian & Developmental Pediatrician
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Guardian <span class="text-danger">*</span>
                    </label>
                    <select name="guardian_id"
                            class="form-select @error('guardian_id') is-invalid @enderror"
                            required>
                        <option value="">-- Select Guardian --</option>
                        @foreach($guardians as $g)
                            <option value="{{ $g->guardian_id }}"
                                    {{ old('guardian_id') == $g->guardian_id ? 'selected' : '' }}>
                                {{ $g->user?->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Developmental Pediatrician
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <select name="dev_ped_id"
                            class="form-select @error('dev_ped_id') is-invalid @enderror">
                        <option value="">-- None / Unknown --</option>
                        @foreach($devPeds as $ped)
                            <option value="{{ $ped->dev_ped_id }}"
                                    {{ old('dev_ped_id') == $ped->dev_ped_id ? 'selected' : '' }}>
                                {{ $ped->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('dev_ped_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Dev. Ped. Document
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <label for="devPedDocInput"
                               class="btn btn-sm btn-outline-secondary mb-0">
                            <i class="bi bi-file-earmark me-1"></i>Choose File
                        </label>
                        <input type="file" name="dev_ped_document" id="devPedDocInput"
                               class="d-none @error('dev_ped_document') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <span id="devPedFileName" class="text-muted small">No file chosen</span>
                    </div>
                    @error('dev_ped_document')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">PDF, JPG, or PNG · Max 50MB</small>
                </div>
            </div>

            {{-- Service & Disability --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-heart-pulse me-1"></i>Service & Disability
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Service Type <span class="text-danger">*</span>
                    </label>
                    <select name="service_type_id" id="service_type_id"
                            class="form-select @error('service_type_id') is-invalid @enderror"
                            required>
                        <option value="">-- Select Service --</option>
                        @foreach($serviceTypes as $st)
                            <option value="{{ $st->service_type_id }}"
                                    {{ old('service_type_id') == $st->service_type_id ? 'selected' : '' }}>
                                {{ $st->service_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 {{ $initIsSpED ? '' : 'd-none' }}"
                     id="programLevelRow">
                    <label class="form-label fw-semibold">
                        Program Level <span class="text-danger">*</span>
                    </label>
                    <select name="program_level_id" id="program_level_id"
                            class="form-select @error('program_level_id') is-invalid @enderror">
                        <option value="">-- Select Program --</option>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    {{ old('program_level_id') == $level->program_level_id ? 'selected' : '' }}>
                                {{ $level->program_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_level_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Disability / Condition <span class="text-danger">*</span>
                    </label>
                    <select name="disability_id" id="disability_id"
                            class="form-select @error('disability_id') is-invalid @enderror"
                            required>
                        @if($initServiceId)
                            <option value="">-- Select Disability --</option>
                            @foreach($disabilities->where('service_type_id', $initServiceId) as $d)
                                <option value="{{ $d->disability_id }}"
                                        {{ old('disability_id') == $d->disability_id ? 'selected' : '' }}>
                                    {{ $d->disability_name }}
                                </option>
                            @endforeach
                        @else
                            <option value="">-- Select Service First --</option>
                        @endif
                    </select>
                    @error('disability_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-8 {{ $initIsOthers ? '' : 'd-none' }}"
                     id="disabilityOtherRow">
                    <label class="form-label fw-semibold">
                        Please Specify <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="disability_other" id="disability_other"
                           class="form-control @error('disability_other') is-invalid @enderror"
                           value="{{ old('disability_other') }}"
                           placeholder="Describe the disability or condition">
                    @error('disability_other')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Add Student
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

<div id="page-data"
     data-disabilities="{{ json_encode($disabilities) }}"
     data-sped-id="{{ $spedId }}"></div>

<script>
// ── Profile picture ──────────────────────────────────────────────────────────
document.getElementById('profilePicInput').addEventListener('change', function () {
    var file = this.files[0];
    if (!file) return;
    document.getElementById('picFileName').textContent = file.name;
    var reader = new FileReader();
    reader.onload = function (e) {
        var w = document.getElementById('avatarWrapper');
        w.innerHTML = '<img src="' + e.target.result + '"'
            + ' style="width:48px;height:48px;border-radius:50%;'
            + 'object-fit:cover;cursor:pointer;flex-shrink:0;">';
        w.querySelector('img').addEventListener('click', openFullscreen);
    };
    reader.readAsDataURL(file);
});

function openFullscreen(e) {
    document.getElementById('fullscreenImg').src = e.target.src;
    new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
}

// ── Dev ped document filename ────────────────────────────────────────────────
document.getElementById('devPedDocInput').addEventListener('change', function () {
    document.getElementById('devPedFileName').textContent =
        this.files[0] ? this.files[0].name : 'No file chosen';
});

// ── Middle initial auto-display ──────────────────────────────────────────────
document.getElementById('middleNameInput').addEventListener('input', function () {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

// ── Age auto-display ─────────────────────────────────────────────────────────
document.getElementById('birthdateInput').addEventListener('change', function () {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    var birth = new Date(this.value);
    var today = new Date();
    var age   = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() &&
        today.getDate()  < birth.getDate())) { age--; }
    document.getElementById('ageDisplay').value = age + ' years old';
});

// ── Sex specify ──────────────────────────────────────────────────────────────
document.getElementById('sexSelect').addEventListener('change', function () {
    document.getElementById('sexSpecifyWrapper').style.display =
        this.value === 'others' ? '' : 'none';
});

// ── Service → Disability cascade ─────────────────────────────────────────────
var pageData         = document.getElementById('page-data').dataset;
var allDisabilities  = JSON.parse(pageData.disabilities);
var spedId           = parseInt(pageData.spedId) || 0;
var serviceSelect    = document.getElementById('service_type_id');
var programLevelRow  = document.getElementById('programLevelRow');
var programLevelSel  = document.getElementById('program_level_id');
var disabilitySelect = document.getElementById('disability_id');
var disabOtherRow    = document.getElementById('disabilityOtherRow');
var disabOtherInput  = document.getElementById('disability_other');

function onServiceChange() {
    var selectedId = parseInt(serviceSelect.value) || 0;
    var filtered   = allDisabilities.filter(function (d) {
        return d.service_type_id === selectedId;
    });

    disabilitySelect.innerHTML = '<option value="">-- Select Disability --</option>';
    filtered.forEach(function (d) {
        var opt         = document.createElement('option');
        opt.value       = d.disability_id;
        opt.textContent = d.disability_name;
        disabilitySelect.appendChild(opt);
    });

    var isSpED = selectedId === spedId;
    programLevelRow.classList.toggle('d-none', !isSpED);
    if (!isSpED) { programLevelSel.value = ''; }

    disabOtherRow.classList.add('d-none');
    disabOtherInput.value = '';
}

function onDisabilityChange() {
    var opt      = disabilitySelect.options[disabilitySelect.selectedIndex];
    var isOthers = opt && opt.text === 'Others';
    disabOtherRow.classList.toggle('d-none', !isOthers);
    if (!isOthers) { disabOtherInput.value = ''; }
}

serviceSelect.addEventListener('change', onServiceChange);
disabilitySelect.addEventListener('change', onDisabilityChange);
</script>
@endsection