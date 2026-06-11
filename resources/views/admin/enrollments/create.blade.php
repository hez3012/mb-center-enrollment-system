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
                                    {{ old('student_id') == $student->student_id ? 'selected' : '' }}>
                                {{ $student->list_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    @error('school_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Program Level <span class="text-danger">*</span>
                    </label>
                    <select name="program_level_id" id="programLevelSelect"
                            class="form-select @error('program_level_id') is-invalid @enderror"
                            required>
                        <option value="">-- Select Program Level --</option>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    {{ old('program_level_id') == $level->program_level_id ? 'selected' : '' }}>
                                {{ $level->program_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_level_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Enrollment Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="enrollment_date"
                           class="form-control @error('enrollment_date') is-invalid @enderror"
                           value="{{ old('enrollment_date', now()->format('Y-m-d')) }}"
                           required>
                    @error('enrollment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Status <span class="text-danger">*</span>
                    </label>
                    {{-- Populated dynamically by JS based on required doc statuses --}}
                    <select name="status" id="enrollmentStatus"
                            class="form-select @error('status') is-invalid @enderror"
                            required>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted" id="statusHint"></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Remarks <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="remarks"
                           class="form-control @error('remarks') is-invalid @enderror"
                           value="{{ old('remarks') }}"
                           placeholder="e.g. Early enrollee, transferred from another center">
                    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-file-earmark-check me-1"></i>Document Checklist
            </p>
            <div class="border rounded p-3 mb-4">
                <p class="text-muted small mb-3">
                    Upload a scanned copy for each document.
                    Status is automatically set to <strong>Missing</strong> until a file is uploaded.
                </p>
                @foreach($documentTypes as $docType)
                    @php
                        $oldDocStatus    = old("doc_status.{$docType->document_type_id}");
                        $initiallyHasFile = in_array($oldDocStatus, ['pending', 'submitted']);
                    @endphp
                    <div class="border rounded p-3 mb-2 {{ $docType->is_required ? 'border-warning' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="fw-semibold small">{{ $docType->document_name }}</span>
                                @if($docType->is_required)
                                    <span class="badge bg-warning text-dark ms-1"
                                          style="font-size:10px;">Required</span>
                                @else
                                    <span class="badge bg-light text-muted ms-1 border"
                                          style="font-size:10px;">Optional</span>
                                @endif
                            </div>
                            <select name="doc_status[{{ $docType->document_type_id }}]"
                                    class="form-select form-select-sm doc-status-select {{ $docType->is_required ? 'doc-status-required' : '' }}"
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
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">
                                    Upload (optional)
                                </label>
                                <input type="file"
                                       name="doc_file[{{ $docType->document_type_id }}]"
                                       class="form-control form-control-sm"
                                       data-doc-type="{{ $docType->document_type_id }}"
                                       accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Notes</label>
                                <input type="text"
                                       name="doc_notes[{{ $docType->document_type_id }}]"
                                       class="form-control form-control-sm"
                                       placeholder="e.g. Original copy received"
                                       value="{{ old("doc_notes.{$docType->document_type_id}") }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-clipboard-check me-1"></i>Create Enrollment
            </button>
        </form>
    </div>
</div>

<script>
// ── Student → Program Level auto-select ─────────────────────────────────────
var studentSelect = document.getElementById('studentSelect');
var programSelect = document.getElementById('programLevelSelect');

studentSelect.addEventListener('change', function () {
    var opt       = this.options[this.selectedIndex];
    var programId = opt ? opt.dataset.program : '';
    if (programId) {
        Array.from(programSelect.options).forEach(function (o) {
            o.selected = (o.value === programId);
        });
    }
});

// ── Document status lock / unlock ────────────────────────────────────────────
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
    // Initialize locked state
    if (select.dataset.hasFile === '0') {
        lockDocSelect(select);
    }

    // Listen for file input changes
    var docTypeId = select.dataset.docType;
    var fileInput = document.querySelector(
        'input[type="file"][data-doc-type="' + docTypeId + '"]'
    );
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                unlockDocSelect(select);
            } else if (select.dataset.hasFile === '0') {
                lockDocSelect(select);
                updateStatusOptions();
            }
        });
    }

    // Sync changes from unlocked selects to enrollment status
    select.addEventListener('change', function () {
        updateStatusOptions();
    });
});

// ── Enrollment status dropdown — driven by required doc statuses ─────────────
function updateStatusOptions() {
    var statusSelect     = document.getElementById('enrollmentStatus');
    var requiredSelects  = document.querySelectorAll('.doc-status-required');
    var current          = statusSelect.value;

    var statuses     = Array.from(requiredSelects).map(function (s) { return s.value; });
    var allSubmitted = statuses.length > 0 && statuses.every(function (s) { return s === 'submitted'; });
    var anyMissing   = statuses.some(function (s) { return s === 'missing'; });

    var options;
    var hint = '';

    if (requiredSelects.length === 0) {
        options = [
            { value: 'pending_payment', label: 'Pending Payment' },
            { value: 'enrolled',        label: 'Enrolled — Payment Confirmed' },
            { value: 'pending',         label: 'Pending Review' },
            { value: 'withdrawn',       label: 'Withdrawn' },
        ];
    } else if (allSubmitted) {
        options = [
            { value: 'pending_payment', label: 'Pending Payment' },
            { value: 'withdrawn',       label: 'Withdrawn' },
        ];
        hint = 'All required documents submitted — enrollment can proceed.';
    } else if (anyMissing) {
        options = [
            { value: 'pending',   label: 'Pending Review' },
            { value: 'rejected',  label: 'Rejected' },
            { value: 'withdrawn', label: 'Withdrawn' },
        ];
        hint = 'One or more required documents are marked as missing.';
    } else {
        options = [
            { value: 'pending', label: 'Pending Review' },
        ];
        hint = 'Set all required documents to Submitted to unlock more status options.';
    }

    statusSelect.innerHTML = '';
    options.forEach(function (opt) {
        var el         = document.createElement('option');
        el.value       = opt.value;
        el.textContent = opt.label;
        if (current === opt.value) { el.selected = true; }
        statusSelect.appendChild(el);
    });

    if (!options.find(function (o) { return o.value === current; })) {
        statusSelect.value = options[0].value;
    }

    document.getElementById('statusHint').textContent = hint;
}

// Run on page load
updateStatusOptions();
</script>
@endsection