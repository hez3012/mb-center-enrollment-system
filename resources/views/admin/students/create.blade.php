@extends('admin.layouts.app')
@section('title', 'Add New Student')
@section('content')

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
                    <input type="file" name="profile_picture"
                           class="form-control @error('profile_picture') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png">
                    @error('profile_picture')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG or PNG only · Max 2MB · Optional</small>
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
                           value="{{ old('first_name') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name') }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light"
                           readonly placeholder="Auto"
                           value="{{ old('middle_name') ? strtoupper(substr(old('middle_name'),0,1)).'.' : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="birthdate" id="birthdateInput"
                           class="form-control @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate') }}" required>
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" id="ageDisplay" class="form-control bg-light"
                           readonly placeholder="Auto">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Sex <span class="text-danger">*</span>
                    </label>
                    <select name="sex" id="sexSelect"
                            class="form-select @error('sex') is-invalid @enderror" required>
                        <option value="">-- Select --</option>
                        @foreach(['male'=>'Male','female'=>'Female','others'=>'Others','prefer_not_to_say'=>'Prefer not to say'] as $val => $label)
                            <option value="{{ $val }}" {{ old('sex') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4" id="sexSpecifyWrapper" style="display:none;">
                    <label class="form-label fw-semibold">Please specify</label>
                    <input type="text" name="sex_specify"
                           class="form-control @error('sex_specify') is-invalid @enderror"
                           value="{{ old('sex_specify') }}">
                    @error('sex_specify')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Address --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Address
            </p>
            <div class="mb-4">
                @include('partials.address-fields', [
                    'fieldPrefix' => '',
                    'data' => [
                        'region'        => old('region', ''),
                        'province'      => old('province', ''),
                        'city'          => old('city', ''),
                        'barangay'      => old('barangay', ''),
                        'house_unit_no' => old('house_unit_no', ''),
                        'street'        => old('street', ''),
                        'zip_code'      => old('zip_code', ''),
                    ],
                    'regions'   => $regions,
                    'provinces' => $provinces,
                    'cities'    => $cities,
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
                                    {{ old('guardian_id') == $guardian->guardian_id ? 'selected' : '' }}>
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
                    <select name="program_level_id"
                            class="form-select @error('program_level_id') is-invalid @enderror" required>
                        <option value="">-- Select Program --</option>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    {{ old('program_level_id') == $level->program_level_id ? 'selected' : '' }}>
                                {{ $level->program_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_level_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                    {{ old('dev_ped_id') == $ped->dev_ped_id ? 'selected' : '' }}>
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
                    <select name="status"
                            class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['active'=>'Active','inactive'=>'Inactive','withdrawn'=>'Withdrawn','completed'=>'Completed'] as $val => $label)
                            <option value="{{ $val }}"
                                    {{ old('status', 'active') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">
                        Disabilities
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <div class="row g-2">
                        @foreach($disabilities as $disability)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="disabilities[]"
                                           value="{{ $disability->disability_id }}"
                                           id="dis_{{ $disability->disability_id }}"
                                           {{ in_array($disability->disability_id, old('disabilities', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small"
                                           for="dis_{{ $disability->disability_id }}">
                                        {{ $disability->disability_name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Create Student
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('middleNameInput').addEventListener('input', function() {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

document.getElementById('birthdateInput').addEventListener('change', function() {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    var birth = new Date(this.value);
    var today = new Date();
    var age = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) { age--; }
    document.getElementById('ageDisplay').value = age + ' years old';
});

document.getElementById('sexSelect').addEventListener('change', function() {
    var wrapper = document.getElementById('sexSpecifyWrapper');
    wrapper.style.display = this.value === 'others' ? '' : 'none';
});

if (document.getElementById('sexSelect').value === 'others') {
    document.getElementById('sexSpecifyWrapper').style.display = '';
}
</script>
@endsection