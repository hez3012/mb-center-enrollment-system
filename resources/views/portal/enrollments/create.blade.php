@extends('portal.layouts.app')
@section('title', 'Submit Enrollment')
@section('content')

@php
    $initServiceId = (int) old('service_type_id', 0);
    $initDisabId   = (int) old('disability_id', 0);
    $initIsSpED    = $initServiceId === (int) $spedId;
    $initIsOthers  = \App\Models\Disability::find($initDisabId)?->disability_name === 'Others';
    $currentSex    = old('student_sex');
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Submit New Enrollment</h5>
    <a href="{{ route('portal.enrollments.index') }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="alert alert-info py-2 small mb-3">
    <i class="bi bi-info-circle me-1"></i>
    Fill in your child's information and upload the required documents.
    Our staff will review your application and contact you via <strong>Messenger</strong>.
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>
        Please review the fields below and correct any errors before resubmitting.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('portal.enrollments.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Child's Personal Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person me-1"></i>Child's Personal Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="student_first_name" id="studentFirstName"
                           class="form-control @error('student_first_name') is-invalid @enderror"
                           value="{{ old('student_first_name') }}"
                           required minlength="2">
                    @error('student_first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="student_middle_name" id="studentMiddleName"
                           class="form-control @error('student_middle_name') is-invalid @enderror"
                           value="{{ old('student_middle_name') }}">
                    @error('student_middle_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="student_last_name"
                           class="form-control @error('student_last_name') is-invalid @enderror"
                           value="{{ old('student_last_name') }}"
                           required minlength="2">
                    @error('student_last_name')
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
                    <select name="student_sex" id="studentSexSelect"
                            class="form-select @error('student_sex') is-invalid @enderror"
                            required>
                        <option value="">-- Select --</option>
                        @foreach(['male'=>'Male','female'=>'Female','prefer_not_to_say'=>'Prefer not to say','others'=>'Others'] as $val => $lbl)
                            <option value="{{ $val }}"
                                    {{ $currentSex === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_sex')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3" id="sexSpecifyWrapper" style="display:none;">
                    <label class="form-label fw-semibold">Please Specify</label>
                    <input type="text" name="student_sex_specify"
                           class="form-control @error('student_sex_specify') is-invalid @enderror"
                           value="{{ old('student_sex_specify') }}">
                    @error('student_sex_specify')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="student_birthdate" id="studentBirthdate"
                           class="form-control @error('student_birthdate') is-invalid @enderror"
                           value="{{ old('student_birthdate') }}" required>
                    @error('student_birthdate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" id="ageDisplay" class="form-control bg-light"
                           readonly placeholder="Auto">
                </div>
            </div>

            {{-- Address --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Child's Address
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

            {{-- Service & Condition --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-heart-pulse me-1"></i>Service & Condition
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

            {{-- Required Documents --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-file-earmark-check me-1"></i>Required Documents
            </p>
            <div class="border rounded p-3 mb-4">
                <p class="text-muted small mb-3">
                    Upload a scanned or photo copy of each required document.
                </p>
                @foreach($documentTypes as $docType)
                    <div class="border rounded p-3 mb-2 border-warning">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="fw-semibold small">
                                    {{ $docType->document_name }}
                                </span>
                                <span class="badge bg-warning text-dark ms-1"
                                      style="font-size:10px;">Required</span>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <label for="portalDocFile{{ $docType->document_type_id }}"
                                           class="btn btn-sm btn-outline-secondary mb-0">
                                        <i class="bi bi-paperclip me-1"></i>Choose File
                                    </label>
                                    <input type="file"
                                           name="doc_file[{{ $docType->document_type_id }}]"
                                           id="portalDocFile{{ $docType->document_type_id }}"
                                           class="d-none"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <span class="text-muted small"
                                          id="portalDocName{{ $docType->document_type_id }}">
                                        No file chosen
                                    </span>
                                </div>
                                <small class="text-muted">PDF, JPG, PNG · Max 50MB</small>
                            </div>
                            <div class="col-md-5">
                                <input type="text"
                                       name="doc_notes[{{ $docType->document_type_id }}]"
                                       class="form-control form-control-sm"
                                       placeholder="Notes (optional)"
                                       value="{{ old("doc_notes.{$docType->document_type_id}") }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Remarks + Waiver --}}
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">
                        Remarks
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="remarks"
                           class="form-control @error('remarks') is-invalid @enderror"
                           value="{{ old('remarks') }}"
                           placeholder="Any additional information for our staff">
                    @error('remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 d-flex align-items-center pt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="waiver_signed" id="waiverSigned" value="1"
                               {{ old('waiver_signed') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="waiverSigned">
                            I agree to the Waiver
                        </label>
                    </div>
                </div>
            </div>

            {{-- Data Privacy --}}
            <div class="mb-4">
                <div class="border rounded">
                    <div class="d-flex align-items-center justify-content-between
                                px-3 py-2 bg-light rounded-top"
                         style="cursor:pointer;"
                         data-bs-toggle="collapse"
                         data-bs-target="#dataPrivacyCollapse">
                        <span class="fw-semibold small">
                            <i class="bi bi-shield-lock text-primary me-1"></i>
                            Data Privacy Notice
                            <span class="fw-normal text-muted ms-1">(click to expand)</span>
                        </span>
                        <i class="bi bi-chevron-down text-muted"></i>
                    </div>
                    <div class="collapse" id="dataPrivacyCollapse">
                        <div class="p-3 border-top small text-muted">
                            <p class="mb-2">
                                In accordance with <strong>Republic Act No. 10173</strong>
                                (Data Privacy Act of 2012), M.B. Therapy Center is
                                committed to protecting the privacy of all individuals
                                whose personal information is collected, stored, and
                                processed through this system.
                            </p>
                            <p class="mb-2">
                                The information collected during enrollment — including the
                                student's name, birthdate, address, disability
                                classification, and developmental history — will be used
                                solely for enrollment processing, program planning,
                                progress monitoring, and guardian communication.
                            </p>
                            <p class="mb-2">
                                This information will not be shared with any third party
                                without prior written consent, except as required by law
                                or for the student's therapeutic and educational progress.
                            </p>
                            <p class="mb-0">
                                Guardians have the right to access, correct, or request
                                deletion of personal data by contacting the center's
                                administration directly.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox"
                           id="dataPrivacyCheck" required>
                    <label class="form-check-label fw-semibold small"
                           for="dataPrivacyCheck">
                        I have read and I agree to the Data Privacy Notice above.
                        <span class="text-danger">*</span>
                    </label>
                </div>
            </div>

            <div class="alert alert-warning py-2 small">
                <i class="bi bi-exclamation-triangle me-1"></i>
                By submitting, you confirm that all information is accurate. Our staff will
                contact you via <strong>Messenger</strong> once your application is reviewed.
            </div>

            <button type="submit" class="btn btn-primary mt-2">
                <i class="bi bi-send me-1"></i>Submit Enrollment
            </button>
        </form>
    </div>
</div>

<div id="page-data"
     data-disabilities="{{ json_encode($disabilities) }}"
     data-sped-id="{{ $spedId }}"></div>

<script>
// ── Middle initial ────────────────────────────────────────────────────────────
document.getElementById('studentMiddleName').addEventListener('input', function () {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

// ── Age auto-display ──────────────────────────────────────────────────────────
function calcAge(val) {
    if (!val) { document.getElementById('ageDisplay').value = ''; return; }
    var birth = new Date(val);
    var today = new Date();
    var age   = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() &&
        today.getDate()  < birth.getDate())) { age--; }
    document.getElementById('ageDisplay').value = age + ' years old';
}

document.getElementById('studentBirthdate').addEventListener('change', function () {
    calcAge(this.value);
});

// Initialize age if old() has a value
calcAge(document.getElementById('studentBirthdate').value);

// ── Sex specify ───────────────────────────────────────────────────────────────
document.getElementById('studentSexSelect').addEventListener('change', function () {
    document.getElementById('sexSpecifyWrapper').style.display =
        this.value === 'others' ? '' : 'none';
});

if (document.getElementById('studentSexSelect').value === 'others') {
    document.getElementById('sexSpecifyWrapper').style.display = '';
}

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

// ── Document file choose ──────────────────────────────────────────────────────
document.querySelectorAll('[id^="portalDocFile"]').forEach(function (input) {
    var docTypeId = input.id.replace('portalDocFile', '');
    input.addEventListener('change', function () {
        var label = document.getElementById('portalDocName' + docTypeId);
        if (label) {
            label.textContent = this.files[0] ? this.files[0].name : 'No file chosen';
        }
    });
});
</script>
@endsection