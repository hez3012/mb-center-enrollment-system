@extends('admin.layouts.app')
@section('title', 'Edit Student')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Edit Student — {{ $student->list_name }}</h5>
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.students.update', $student->student_id) }}"
              enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- Personal Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person me-1"></i>Personal Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $student->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name', $student->middle_name) }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $student->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light" readonly
                           value="{{ $student->middle_initial }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-muted small fw-normal">(mm/dd/yyyy)</span>
                    </label>
                    <input type="date" name="birthdate" id="birthdateInput"
                           class="form-control @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate', $student->birthdate->format('Y-m-d')) }}" required>
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" id="ageDisplay" class="form-control bg-light" readonly
                           value="{{ $student->age !== null ? $student->age . ' years old' : '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sex</label>
                    <select name="sex" id="sexSelect"
                            class="form-select @error('sex') is-invalid @enderror" required>
                        <option value="male"              {{ old('sex', $student->sex) == 'male'              ? 'selected' : '' }}>Male</option>
                        <option value="female"            {{ old('sex', $student->sex) == 'female'            ? 'selected' : '' }}>Female</option>
                        <option value="others"            {{ old('sex', $student->sex) == 'others'            ? 'selected' : '' }}>Others (please specify)</option>
                        <option value="prefer_not_to_say" {{ old('sex', $student->sex) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                    @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div id="sexSpecifyDiv"
                         class="mt-2 {{ old('sex', $student->sex) !== 'others' ? 'd-none' : '' }}">
                        <input type="text" name="sex_specify"
                               class="form-control @error('sex_specify') is-invalid @enderror"
                               placeholder="Please specify"
                               value="{{ old('sex_specify', $student->sex_specify) }}">
                        @error('sex_specify')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" id="statusSelect"
                            class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['active','inactive','withdrawn','completed'] as $s)
                            <option value="{{ $s }}"
                                    {{ old('status', $student->status) == $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" id="statusDesc"></small>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Contact #1 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_1"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1', $student->contact_number_1) }}">
                    @error('contact_number_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Contact #2 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_2"
                           class="form-control @error('contact_number_2') is-invalid @enderror"
                           value="{{ old('contact_number_2', $student->contact_number_2) }}">
                    @error('contact_number_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        'region'        => old('region',        $student->region        ?? ''),
                        'province'      => old('province',      $student->province      ?? ''),
                        'city'          => old('city',          $student->city          ?? ''),
                        'barangay'      => old('barangay',      $student->barangay      ?? ''),
                        'house_unit_no' => old('house_unit_no', $student->house_unit_no ?? ''),
                        'street'        => old('street',        $student->street        ?? ''),
                        'zip_code'      => old('zip_code',      $student->zip_code      ?? ''),
                    ],
                    'regions'   => $regions,
                    'provinces' => $provinces,
                    'cities'    => $cities,
                ])
            </div>

            {{-- Academic Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-mortarboard me-1"></i>Academic Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Guardian</label>
                    <select name="guardian_id"
                            class="form-select @error('guardian_id') is-invalid @enderror" required>
                        @foreach($guardians as $guardian)
                            <option value="{{ $guardian->guardian_id }}"
                                    {{ old('guardian_id', $student->guardian_id) == $guardian->guardian_id ? 'selected' : '' }}>
                                {{ $guardian->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Program Level</label>
                    <select name="program_level_id" id="programLevelSelect"
                            class="form-select @error('program_level_id') is-invalid @enderror" required>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    data-desc="{{ $level->description }}"
                                    {{ old('program_level_id', $student->program_level_id) == $level->program_level_id ? 'selected' : '' }}>
                                {{ $level->program_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" id="programDesc"></small>
                    @error('program_level_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Developmental Pediatrician
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <select name="dev_ped_id"
                            class="form-select @error('dev_ped_id') is-invalid @enderror">
                        <option value="">-- None --</option>
                        @foreach($devPeds as $devPed)
                            <option value="{{ $devPed->dev_ped_id }}"
                                    {{ old('dev_ped_id', $student->dev_ped_id) == $devPed->dev_ped_id ? 'selected' : '' }}>
                                {{ $devPed->full_name }} — {{ $devPed->clinic_hospital }}
                            </option>
                        @endforeach
                    </select>
                    @error('dev_ped_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Dev. Ped. Assessment Document
                        <span class="text-muted small fw-normal">(upload to replace existing)</span>
                    </label>
                    @if($student->dev_ped_document)
                        <div class="mb-2">
                            <a href="{{ Storage::url($student->dev_ped_document) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-file-earmark me-1"></i>View Current Document
                            </a>
                        </div>
                    @endif
                    <input type="file" name="dev_ped_document"
                           class="form-control @error('dev_ped_document') is-invalid @enderror"
                           accept=".pdf,.jpg,.jpeg,.png">
                    @error('dev_ped_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Disability Classifications --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-clipboard-pulse me-1"></i>Disability Classifications
            </p>
            <div class="border rounded p-3 mb-4 bg-light">
                <p class="text-muted small mb-3">Select all that apply.</p>
                <div class="row g-2">
                    @foreach($disabilities as $disability)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input disability-check"
                                   type="checkbox"
                                   name="disabilities[]"
                                   value="{{ $disability->disability_id }}"
                                   id="dis_{{ $disability->disability_id }}"
                                   {{ $student->disabilities->contains('disability_id', $disability->disability_id) ? 'checked' : '' }}>
                            <label class="form-check-label small"
                                   for="dis_{{ $disability->disability_id }}">
                                {{ $disability->disability_name }}
                            </label>
                        </div>
                        @if($disability->disability_name === 'Others')
                        <div id="othersSpecifyDiv"
                             class="mt-2 ms-4 {{ !$student->disabilities->contains('disability_id', $disability->disability_id) ? 'd-none' : '' }}">
                            <input type="text" name="disability_others_specify"
                                   class="form-control form-control-sm"
                                   placeholder="Please specify condition">
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
    </div>
</div>

<script>
// M.I. auto-fill
document.getElementById('middleNameInput').addEventListener('input', function () {
    const mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

// Age auto-fill
document.getElementById('birthdateInput').addEventListener('change', function () {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    const birth = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
    document.getElementById('ageDisplay').value = age + ' years old';
});

// Sex "Others" toggle
document.getElementById('sexSelect').addEventListener('change', function () {
    document.getElementById('sexSpecifyDiv').classList.toggle('d-none', this.value !== 'others');
});

// Status description
const statusDescriptions = {
    active:    'Student is currently enrolled and attending the program.',
    inactive:  'Student account is disabled and not currently attending.',
    withdrawn: 'Student has officially withdrawn from the program.',
    completed: 'Student has successfully completed the program.',
};
const statusSel  = document.getElementById('statusSelect');
const statusDesc = document.getElementById('statusDesc');
statusSel.addEventListener('change', function () {
    statusDesc.textContent = statusDescriptions[this.value] || '';
});
statusDesc.textContent = statusDescriptions[statusSel.value] || '';

// Program Level description
const programDesc = document.getElementById('programDesc');
const programSel  = document.getElementById('programLevelSelect');
programSel.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    programDesc.textContent = opt ? (opt.getAttribute('data-desc') || '') : '';
});
programDesc.textContent = (programSel.options[programSel.selectedIndex]?.getAttribute('data-desc') || '');

// Disability "Others" toggle
document.querySelectorAll('.disability-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const label = document.querySelector(`label[for="${this.id}"]`);
        if (label && label.textContent.trim() === 'Others') {
            const div = document.getElementById('othersSpecifyDiv');
            if (div) div.classList.toggle('d-none', !this.checked);
        }
    });
});
</script>
@endsection