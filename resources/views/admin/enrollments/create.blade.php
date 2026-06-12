@extends('admin.layouts.app')
@section('title', 'Add Walk-in Enrollment')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Add Walk-in Enrollment</h5>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.enrollments.store') }}"
              enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="enrollment_type" value="walk_in">

            {{-- Enrollment Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-clipboard-check me-1"></i>Enrollment Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Student <span class="text-danger">*</span>
                    </label>
                    <select name="student_id" id="studentSelect"
                            class="form-select @error('student_id') is-invalid @enderror"
                            required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->student_id }}"
                                    data-program="{{ $student->program_level_id }}"
                                    data-is-sped="{{ $student->service_type_id == $spedId ? '1' : '0' }}"
                                    {{ old('student_id') == $student->student_id ? 'selected' : '' }}>
                                {{ $student->list_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        School Year <span class="text-danger">*</span>
                    </label>
                    <select name="school_year_id"
                            class="form-select @error('school_year_id') is-invalid @enderror"
                            required>
                        <option value="">-- Select School Year --</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy->school_year_id }}"
                                    {{ (old('school_year_id') == $sy->school_year_id ||
                                        ($currentYear && $currentYear->school_year_id == $sy->school_year_id))
                                        ? 'selected' : '' }}>
                                {{ $sy->year_label }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_year_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 d-none" id="programLevelGroup">
                    <label class="form-label fw-semibold">
                        Program Level <span class="text-danger">*</span>
                    </label>
                    <select name="program_level_id" id="programLevelSelect"
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

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Enrollment Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="enrollment_date"
                           class="form-control @error('enrollment_date') is-invalid @enderror"
                           value="{{ old('enrollment_date', now()->format('Y-m-d')) }}"
                           required>
                    @error('enrollment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select name="status" id="enrollmentStatus"
                            class="form-select @error('status') is-invalid @enderror"
                            required>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted" id="statusHint"></small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Remarks
                        <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="remarks"
                           class="form-control @error('remarks') is-invalid @enderror"
                           value="{{ old('remarks') }}">
                    @error('remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 d-flex align-items-center pt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="waiver_signed" id="waiverSigned" value="1"
                               {{ old('waiver_signed') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="waiverSigned">
                            Waiver Signed by Guardian
                        </label>
                    </div>
                </div>
            </div>

            {{-- Document Checklist --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-file-earmark-check me-1"></i>Document Checklist
            </p>
            <div class="border rounded p-3 mb-4">
                <p class="text-muted small mb-3">
                    Upload scanned copies. Status is locked to
                    <strong>Missing</strong> until a file is uploaded.
                </p>
                @foreach($documentTypes as $docType)
                    @php
                        $oldDocStatus     = old("doc_status.{$docType->document_type_id}");
                        $initiallyHasFile = in_array($oldDocStatus, ['pending', 'submitted']);
                    @endphp
                    <div class="border rounded p-3 mb-2 border-warning">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="fw-semibold small">
                                    {{ $docType->document_name }}
                                </span>
                                <span class="badge bg-warning text-dark ms-1"
                                      style="font-size:10px;">Required</span>
                            </div>
                            <select name="doc_status[{{ $docType->document_type_id }}]"
                                    class="form-select form-select-sm doc-status-select doc-status-required"
                                    data-has-file="{{ $initiallyHasFile ? '1' : '0' }}"
                                    data-doc-type="{{ $docType->document_type_id }}"
                                    style="width:145px;">
                                @if(!$initiallyHasFile)
                                    <option value="missing" selected>Missing</option>
                                @else
                                    <option value="pending"
                                            {{ $oldDocStatus === 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="submitted"
                                            {{ $oldDocStatus === 'submitted' ? 'selected' : '' }}>
                                        Submitted
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <label for="docFile{{ $docType->document_type_id }}"
                                           class="btn btn-sm btn-outline-secondary mb-0">
                                        <i class="bi bi-paperclip me-1"></i>Choose File
                                    </label>
                                    <input type="file"
                                           name="doc_file[{{ $docType->document_type_id }}]"
                                           id="docFile{{ $docType->document_type_id }}"
                                           class="d-none"
                                           data-doc-type="{{ $docType->document_type_id }}"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <span class="text-muted small"
                                          id="docName{{ $docType->document_type_id }}">
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
                            <span class="fw-normal text-muted ms-1">
                                (click to expand)
                            </span>
                        </span>
                        <i class="bi bi-chevron-down text-muted"></i>
                    </div>
                    <div class="collapse" id="dataPrivacyCollapse">
                        <div class="p-3 border-top small text-muted">
                            <p class="mb-2">
                                In accordance with <strong>Republic Act No. 10173</strong>
                                (Data Privacy Act of 2012), M.B. Therapy Center is
                                committed to protecting the privacy of all individuals
                                whose personal information is collected through this system.
                            </p>
                            <p class="mb-2">
                                The information collected during enrollment — including
                                the student's name, birthdate, address, disability
                                classification, and developmental history — will be used
                                solely for enrollment processing, program planning,
                                progress monitoring, and guardian communication.
                            </p>
                            <p class="mb-2">
                                This information will not be shared with any third party
                                without prior written consent, except as required by law
                                or for the student's therapeutic progress.
                            </p>
                            <p class="mb-0">
                                Guardians have the right to access, correct, or request
                                deletion of personal data by contacting the administration.
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

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-clipboard-check me-1"></i>Create Enrollment
            </button>
        </form>
    </div>
</div>

<div id="page-data" data-sped-id="{{ $spedId }}"></div>

<script>
var spedId          = parseInt(document.getElementById('page-data').dataset.spedId) || 0;
var studentSelect   = document.getElementById('studentSelect');
var programLevelGrp = document.getElementById('programLevelGroup');
var programLevelSel = document.getElementById('programLevelSelect');

// ── Student → Program Level ──────────────────────────────────────────────────
studentSelect.addEventListener('change', function () {
    var opt    = this.options[this.selectedIndex];
    var isSpED = opt.dataset.isSped === '1';
    var progId = opt.dataset.program || '';

    programLevelGrp.classList.toggle('d-none', !isSpED);
    if (isSpED && progId) {
        programLevelSel.value = progId;
    } else if (!isSpED) {
        programLevelSel.value = '';
    }
    updateStatusOptions();
});

// ── Document lock / unlock ───────────────────────────────────────────────────
function lockDocSelect(select) {
    select.innerHTML = '<option value="missing">Missing</option>';
    select.value = 'missing';
    select.style.pointerEvents   = 'none';
    select.style.backgroundColor = '#e9ecef';
    select.style.color           = '#6c757d';
}

function unlockDocSelect(select) {
    var prev = select.value;
    select.innerHTML = '';
    var p = document.createElement('option');
    p.value = 'pending'; p.textContent = 'Pending';
    var s = document.createElement('option');
    s.value = 'submitted'; s.textContent = 'Submitted';
    select.appendChild(p);
    select.appendChild(s);
    select.value = (prev === 'submitted') ? 'submitted' : 'pending';
    select.style.pointerEvents   = '';
    select.style.backgroundColor = '';
    select.style.color           = '';
    updateStatusOptions();
}

document.querySelectorAll('.doc-status-select').forEach(function (select) {
    if (select.dataset.hasFile === '0') { lockDocSelect(select); }

    var docTypeId = select.dataset.docType;
    var fileInput = document.getElementById('docFile' + docTypeId);
    var fileLabel = document.getElementById('docName' + docTypeId);

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                unlockDocSelect(select);
                if (fileLabel) { fileLabel.textContent = this.files[0].name; }
            } else if (select.dataset.hasFile === '0') {
                lockDocSelect(select);
                updateStatusOptions();
                if (fileLabel) { fileLabel.textContent = 'No file chosen'; }
            }
        });
    }

    select.addEventListener('change', updateStatusOptions);
});

// ── Enrollment status (document-driven) ─────────────────────────────────────
function updateStatusOptions() {
    var enrollmentStatus = document.getElementById('enrollmentStatus');
    var required         = document.querySelectorAll('.doc-status-required');
    var statuses         = Array.from(required).map(function (s) { return s.value; });
    var allSubmitted     = statuses.length > 0 &&
        statuses.every(function (s) { return s === 'submitted'; });
    var anyMissing       = statuses.some(function (s) { return s === 'missing'; });
    var current          = enrollmentStatus.value;

    var options, hint;

    if (allSubmitted) {
        options = [
            { value: 'pending_payment', label: 'Pending Payment' },
            { value: 'withdrawn',       label: 'Withdrawn' },
        ];
        hint = 'All required documents submitted.';
    } else if (anyMissing) {
        options = [
            { value: 'pending',   label: 'Pending Review' },
            { value: 'rejected',  label: 'Rejected' },
            { value: 'withdrawn', label: 'Withdrawn' },
        ];
        hint = 'One or more required documents are missing.';
    } else {
        options = [
            { value: 'pending', label: 'Pending Review' },
        ];
        hint = 'Set all required documents to Submitted to unlock Pending Payment.';
    }

    enrollmentStatus.innerHTML = '';
    options.forEach(function (o) {
        var el = document.createElement('option');
        el.value = o.value;
        el.textContent = o.label;
        if (current === o.value) { el.selected = true; }
        enrollmentStatus.appendChild(el);
    });

    if (!options.find(function (o) { return o.value === current; })) {
        enrollmentStatus.value = options[0].value;
    }

    document.getElementById('statusHint').textContent = hint;
}

updateStatusOptions();
</script>
@endsection