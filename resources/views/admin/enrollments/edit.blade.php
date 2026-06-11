@extends('admin.layouts.app')
@section('title', 'Edit Enrollment')
@section('content')

@php
    $hasPayment     = $enrollment->payment !== null;
    $isOnlinePending = $enrollment->status === 'pending'
                       && $enrollment->enrollment_type === 'online';
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Edit Enrollment</h5>
    <a href="{{ route('admin.enrollments.show', $enrollment->enrollment_id) }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST"
              action="{{ route('admin.enrollments.update', $enrollment->enrollment_id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Student info — read-only --}}
            <div class="alert alert-light border mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Student</small>
                        <strong>{{ optional($enrollment->student)->list_name }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">School Year</small>
                        <strong>{{ optional($enrollment->schoolYear)->year_label }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Enrollment Type</small>
                        <span class="badge bg-{{ $enrollment->enrollment_type === 'walk_in' ? 'secondary' : 'info text-dark' }}">
                            {{ $enrollment->type_label }}
                        </span>
                    </div>
                </div>
            </div>

            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-clipboard-check me-1"></i>Enrollment Details
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Program Level</label>
                    <select name="program_level_id"
                            class="form-select @error('program_level_id') is-invalid @enderror"
                            required>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    {{ old('program_level_id', $enrollment->program_level_id) == $level->program_level_id ? 'selected' : '' }}>
                                {{ $level->program_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_level_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Enrollment Date</label>
                    <input type="date" name="enrollment_date"
                           class="form-control @error('enrollment_date') is-invalid @enderror"
                           value="{{ old('enrollment_date', $enrollment->enrollment_date?->format('Y-m-d')) }}"
                           required>
                    @error('enrollment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>

                    @if($isOnlinePending)
                        {{-- Online + pending: locked, use Approve/Reject on View page --}}
                        <input type="hidden" name="status" value="pending">
                        <input type="text" class="form-control bg-light" readonly
                               value="Pending Review">
                        <small class="text-muted mt-1 d-block">
                            <i class="bi bi-info-circle me-1"></i>
                            Use the <strong>Approve / Reject</strong> buttons on the
                            View page first.
                        </small>

                    @elseif($hasPayment)
                        {{-- After payment: Enrolled / Withdrawn / Completed --}}
                        <select name="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required>
                            @foreach([
                                'enrolled'  => 'Enrolled — Payment Confirmed',
                                'withdrawn' => 'Withdrawn',
                                'completed' => 'Completed',
                            ] as $val => $label)
                                <option value="{{ $val }}"
                                        {{ old('status', $enrollment->status) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    @else
                        {{-- Before payment: Pending Payment / Rejected / Withdrawn --}}
                        <select name="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required>
                            @foreach([
                                'pending_payment' => 'Pending Payment',
                                'rejected'        => 'Rejected',
                                'withdrawn'       => 'Withdrawn',
                            ] as $val => $label)
                                <option value="{{ $val }}"
                                        {{ old('status', $enrollment->status) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Remarks <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="remarks"
                           class="form-control @error('remarks') is-invalid @enderror"
                           value="{{ old('remarks', $enrollment->remarks) }}">
                    @error('remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Rejection Reason
                        <span class="text-muted small fw-normal">(if rejected)</span>
                    </label>
                    <input type="text" name="rejection_reason"
                           class="form-control @error('rejection_reason') is-invalid @enderror"
                           value="{{ old('rejection_reason', $enrollment->rejection_reason) }}">
                    @error('rejection_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 d-flex align-items-center pt-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="waiver_signed" id="waiverSigned" value="1"
                               {{ old('waiver_signed', $enrollment->waiver_signed) ? 'checked' : '' }}>
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
                    Upload a new file to replace the existing one.
                    Status is locked to <strong>Missing</strong> until a file is on record.
                </p>
                @foreach($documentTypes as $docType)
                    @php
                        $existing = $enrollment->documents
                            ->where('document_type_id', $docType->document_type_id)
                            ->first();
                        $hasFile         = $existing?->file_path !== null;
                        $currentDocStatus = $hasFile
                            ? ($existing?->submission_status ?? 'pending')
                            : 'missing';
                    @endphp
                    <div class="border rounded p-3 mb-2 {{ $docType->is_required ? 'border-warning' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="fw-semibold small">{{ $docType->document_name }}</span>
                                @if($docType->is_required)
                                    <span class="badge bg-warning text-dark ms-1"
                                          style="font-size:10px;">Required</span>
                                @endif
                            </div>
                            <select name="doc_status[{{ $docType->document_type_id }}]"
                                    class="form-select form-select-sm doc-status-select"
                                    data-has-file="{{ $hasFile ? '1' : '0' }}"
                                    data-doc-type="{{ $docType->document_type_id }}"
                                    style="width:145px;">
                                @if(!$hasFile)
                                    <option value="missing" selected>Missing</option>
                                @else
                                    <option value="pending"
                                            {{ $currentDocStatus === 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="submitted"
                                            {{ $currentDocStatus === 'submitted' ? 'selected' : '' }}>
                                        Submitted
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                @if($existing?->file_path)
                                    <div class="mb-1">
                                        <a href="{{ Storage::url($existing->file_path) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-file-earmark me-1"></i>Current File
                                        </a>
                                    </div>
                                @endif
                                <input type="file"
                                       name="doc_file[{{ $docType->document_type_id }}]"
                                       class="form-control form-control-sm"
                                       data-doc-type="{{ $docType->document_type_id }}"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">JPG, PNG, or PDF · Max 10MB</small>
                            </div>
                            <div class="col-md-6">
                                <input type="text"
                                       name="doc_notes[{{ $docType->document_type_id }}]"
                                       class="form-control form-control-sm"
                                       placeholder="Notes"
                                       value="{{ old("doc_notes.{$docType->document_type_id}", $existing?->notes) }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
    </div>
</div>

<script>
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
}

document.querySelectorAll('.doc-status-select').forEach(function (select) {
    // Initialize locked state for docs with no file
    if (select.dataset.hasFile === '0') {
        lockDocSelect(select);
    }

    var docTypeId = select.dataset.docType;
    var fileInput = document.querySelector(
        'input[type="file"][data-doc-type="' + docTypeId + '"]'
    );
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                unlockDocSelect(select);
            } else if (select.dataset.hasFile === '0') {
                // No existing file and user cleared input — lock again
                lockDocSelect(select);
            }
            // If hasFile = '1' (existing file) and input cleared — keep unlocked
        });
    }
});
</script>
@endsection