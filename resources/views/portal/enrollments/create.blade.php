@extends('portal.layouts.app')
@section('title', 'Submit Enrollment')
@section('content')

@if(!$currentYear)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        There is no active school year configured. Please contact the administrator.
    </div>
@else

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Submit New Enrollment</h5>
    <a href="{{ route('portal.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <p class="fw-semibold mb-1">
            <i class="bi bi-exclamation-circle me-1"></i>Please fix the following:
        </p>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('portal.enrollments.store') }}"
              enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="school_year_id"
                   value="{{ $currentYear->school_year_id }}">

            <div class="alert alert-info py-2 mb-4">
                <i class="bi bi-calendar3 me-1"></i>
                Enrolling for school year: <strong>{{ $currentYear->year_label }}</strong>
            </div>

            {{-- Student Personal Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person me-1"></i>Student Personal Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" required minlength="2">
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
                           value="{{ old('last_name') }}" required minlength="2">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light"
                           readonly placeholder="Auto">
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
                        @foreach(['male'=>'Male','female'=>'Female','prefer_not_to_say'=>'Prefer not to say','others'=>'Others'] as $val => $label)
                            <option value="{{ $val }}"
                                    {{ old('sex') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 {{ old('sex') === 'others' ? '' : 'd-none' }}"
                     id="sexSpecifyWrapper">
                    <label class="form-label fw-semibold">Please specify</label>
                    <input type="text" name="sex_specify"
                           class="form-control @error('sex_specify') is-invalid @enderror"
                           value="{{ old('sex_specify') }}">
                    @error('sex_specify')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Student Address --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Student Address
            </p>
            <div class="mb-4">
                @include('partials.address-fields',[
                    'fieldPrefix' => '',
                    'data' => [
                        'region'        => old('region',''),
                        'province'      => old('province',''),
                        'city'          => old('city',''),
                        'barangay'      => old('barangay',''),
                        'house_unit_no' => old('house_unit_no',''),
                        'street'        => old('street',''),
                        'zip_code'      => old('zip_code',''),
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
                        Program Level <span class="text-danger">*</span>
                    </label>
                    <select name="program_level_id" id="programLevelSelect"
                            class="form-select @error('program_level_id') is-invalid @enderror"
                            required>
                        <option value="">-- Select Program --</option>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    data-desc="{{ $level->description }}"
                                    {{ old('program_level_id') == $level->program_level_id ? 'selected' : '' }}>
                                {{ $level->program_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_level_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                                    {{ old('dev_ped_id') == $ped->dev_ped_id ? 'selected' : '' }}>
                                {{ $ped->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('dev_ped_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                               {{ in_array($disability->disability_id, old('disabilities',[])) ? 'checked' : '' }}>
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
                                           {{ old('disability_other') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="disOthersCheck">
                                        Others (please specify)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 {{ old('disability_other') ? '' : 'd-none' }}"
                                 id="disOthersWrapper">
                                <input type="text" name="disability_other"
                                       class="form-control form-control-sm @error('disability_other') is-invalid @enderror"
                                       placeholder="Please specify other disability"
                                       value="{{ old('disability_other') }}">
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

            {{-- Documents --}}
            @php
                $requiredDocs = $documentTypes->where('is_required', true);
                $optionalDocs = $documentTypes->where('is_required', false);
            @endphp

            @if($requiredDocs->isNotEmpty())
                <p class="fw-semibold text-primary small mb-2">
                    <i class="bi bi-file-earmark-check me-1"></i>Required Documents
                    <span class="text-danger">*</span>
                </p>
                <div class="border rounded p-3 mb-3">
                    <p class="text-muted small mb-3">
                        The following documents are <strong>required</strong> for enrollment.
                    </p>
                    @foreach($requiredDocs as $docType)
                        <div class="border border-warning rounded p-3 mb-2">
                            <div class="mb-2">
                                <span class="fw-semibold small">{{ $docType->document_name }}</span>
                                <span class="badge bg-warning text-dark ms-1"
                                      style="font-size:10px;">Required</span>
                            </div>
                            <input type="file"
                                   name="doc_file[{{ $docType->document_type_id }}]"
                                   class="form-control form-control-sm"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">JPG, PNG, or PDF only · Max 10MB</small>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($optionalDocs->isNotEmpty())
                <p class="fw-semibold text-primary small mb-2">
                    <i class="bi bi-file-earmark me-1"></i>Optional Documents
                </p>
                <div class="border rounded p-3 mb-4">
                    <p class="text-muted small mb-3">
                        The following are <strong>optional</strong> but recommended if available.
                    </p>
                    @foreach($optionalDocs as $docType)
                        <div class="border rounded p-3 mb-2">
                            <div class="mb-2">
                                <span class="fw-semibold small">{{ $docType->document_name }}</span>
                                <span class="badge bg-light text-muted border ms-1"
                                      style="font-size:10px;">Optional</span>
                            </div>
                            <input type="file"
                                   name="doc_file[{{ $docType->document_type_id }}]"
                                   class="form-control form-control-sm"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">JPG, PNG, or PDF only · Max 10MB</small>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Waiver & Data Privacy --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-shield-check me-1"></i>Waiver & Data Privacy Notice
            </p>
            <div class="border rounded p-3 mb-4 bg-light">
                <button type="button" class="btn btn-sm btn-outline-secondary mb-3"
                        id="dpaToggle">
                    <i class="bi bi-eye me-1"></i>View Data Privacy Notice
                </button>

                <div id="dpaContent" class="d-none mb-3 p-3 border rounded bg-white small text-muted">
                    <p class="fw-semibold text-dark mb-2">
                        Data Privacy Notice — Republic Act No. 10173
                    </p>
                    <p class="mb-2">
                        In compliance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong>
                        and its implementing rules and regulations, M.B. Therapy Center hereby
                        informs you of the following:
                    </p>
                    <p class="fw-semibold text-dark mb-1">Purpose of Data Collection</p>
                    <p class="mb-2">
                        The personal information you provide through this enrollment form —
                        including your child's name, age, birthdate, address, diagnosis,
                        disability classification, and guardian details — will be collected
                        and processed solely for enrollment processing, delivery of special
                        education and therapy services, communication between the center and
                        guardians, and compliance with applicable legal and regulatory obligations.
                    </p>
                    <p class="fw-semibold text-dark mb-1">Confidentiality</p>
                    <p class="mb-2">
                        All information collected will be kept strictly confidential and will
                        only be accessed by authorized personnel of M.B. Therapy Center,
                        including the Directress, administrative staff, and assigned teachers
                        directly involved in your child's program.
                    </p>
                    <p class="fw-semibold text-dark mb-1">Sharing of Information</p>
                    <p class="mb-2">
                        Your child's information may be shared with their assigned Developmental
                        Pediatrician for progress reporting and re-evaluation purposes as required
                        every 6–8 months. No information will be shared with any other third party
                        without your explicit written consent.
                    </p>
                    <p class="fw-semibold text-dark mb-1">Your Rights</p>
                    <p class="mb-2">
                        As a data subject, you have the right to: (a) be informed of how your
                        data is processed; (b) access your personal data held by the center;
                        (c) correct any inaccurate or outdated information; (d) object to the
                        processing of your personal data; and (e) file a complaint with the
                        National Privacy Commission if you believe your rights have been violated.
                    </p>
                    <p class="fw-semibold text-dark mb-1">Data Protection Officer</p>
                    <p class="mb-0">
                        For any concerns regarding your personal data, you may contact
                        M.B. Therapy Center at 8 Rongo St., Central Signal Village,
                        Taguig City 1630, or reach us through our official communication channels.
                    </p>
                </div>

                <div class="form-check">
                    <input class="form-check-input @error('waiver_signed') is-invalid @enderror"
                           type="checkbox" name="waiver_signed" id="waiverCheck" value="1"
                           {{ old('waiver_signed') ? 'checked' : '' }} required>
                    <label class="form-check-label fw-semibold small" for="waiverCheck">
                        I have read and understood the Data Privacy Notice above. I agree to
                        the enrollment terms and confirm that all information I have provided
                        is accurate and complete.
                    </label>
                    @error('waiver_signed')
                        <div class="invalid-feedback">
                            You must accept the waiver and data privacy notice to proceed.
                        </div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Submit Enrollment
            </button>
        </form>
    </div>
</div>

@endif

<script>
document.getElementById('middleNameInput').addEventListener('input', function () {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

document.getElementById('birthdateInput').addEventListener('change', function () {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    var birth = new Date(this.value);
    var today = new Date();
    var age   = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) { age--; }
    document.getElementById('ageDisplay').value = age + ' years old';
});

document.getElementById('sexSelect').addEventListener('change', function () {
    document.getElementById('sexSpecifyWrapper')
        .classList.toggle('d-none', this.value !== 'others');
});

document.getElementById('disOthersCheck').addEventListener('change', function () {
    document.getElementById('disOthersWrapper').classList.toggle('d-none', !this.checked);
    if (!this.checked) {
        document.querySelector('input[name="disability_other"]').value = '';
    }
});

document.getElementById('programLevelSelect').addEventListener('change', function () {
    var desc = this.options[this.selectedIndex]?.dataset.desc
               || 'Select a program to see its description.';
    document.getElementById('programLevelDesc').textContent = desc;
});

document.getElementById('dpaToggle').addEventListener('click', function () {
    var content  = document.getElementById('dpaContent');
    var hidden   = content.classList.contains('d-none');
    content.classList.toggle('d-none', !hidden);
    this.innerHTML = hidden
        ? '<i class="bi bi-eye-slash me-1"></i>Hide Data Privacy Notice'
        : '<i class="bi bi-eye me-1"></i>View Data Privacy Notice';
});

(function () {
    var progSel = document.getElementById('programLevelSelect');
    if (progSel && progSel.value) {
        document.getElementById('programLevelDesc').textContent =
            progSel.options[progSel.selectedIndex]?.dataset.desc || '';
    }
}());
</script>
@endsection