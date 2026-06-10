@extends('portal.layouts.app')
@section('title', 'Submit Enrollment')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Submit New Enrollment</h5>
    <a href="{{ route('portal.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if($students->isEmpty())
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-1"></i>
    All of your linked students are already enrolled for the current school year,
    or no active students are linked to your account.
    Please contact the administrator for assistance.
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('portal.enrollments.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Enrollment Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-clipboard-check me-1"></i>Enrollment Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Student</label>
                    <select name="student_id" id="studentSelect"
                            class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->student_id }}"
                                    data-program="{{ $student->program_level_id }}"
                                    {{ old('student_id') == $student->student_id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">School Year</label>
                    @if($currentYear)
                        <input type="text" class="form-control bg-light" readonly
                               value="{{ $currentYear->year_label }}">
                        <input type="hidden" name="school_year_id" value="{{ $currentYear->school_year_id }}">
                    @else
                        <input type="text" class="form-control bg-light" readonly
                               value="No active school year">
                        <small class="text-danger">Please contact the administrator.</small>
                    @endif
                </div>
                <input type="hidden" name="program_level_id" id="programLevelHidden"
                       value="{{ old('program_level_id') }}">
            </div>

            {{-- Required Documents --}}
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
                    Please upload a clear photo or scanned copy of each.
                </p>
                @foreach($requiredDocs as $docType)
                <div class="border border-warning rounded p-3 mb-2">
                    <div class="mb-2">
                        <span class="fw-semibold small">{{ $docType->document_name }}</span>
                        <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">Required</span>
                    </div>
                    <input type="file"
                           name="doc_file[{{ $docType->document_type_id }}]"
                           class="form-control form-control-sm"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">JPG, PNG, or PDF only · Max 5MB</small>
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
                    The following documents are <strong>optional</strong> but recommended if available
                    (e.g., from a previous center or therapy program).
                    You may still submit your enrollment without these.
                </p>
                @foreach($optionalDocs as $docType)
                <div class="border rounded p-3 mb-2">
                    <div class="mb-2">
                        <span class="fw-semibold small">{{ $docType->document_name }}</span>
                        <span class="badge bg-light text-muted border ms-1" style="font-size:10px;">Optional</span>
                    </div>
                    <input type="file"
                           name="doc_file[{{ $docType->document_type_id }}]"
                           class="form-control form-control-sm"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">JPG, PNG, or PDF only · Max 5MB</small>
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

                <div id="dpaContent" style="display:none;"
                     class="mb-3 p-3 border rounded bg-white small text-muted">
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

            @if(!$currentYear)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-1"></i>
                There is no active school year configured at this time.
                Please contact the administrator before submitting.
            </div>
            @else
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Submit Enrollment
            </button>
            @endif

        </form>
    </div>
</div>

<script>
document.getElementById('studentSelect').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    document.getElementById('programLevelHidden').value = opt ? (opt.dataset.program || '') : '';
});

document.getElementById('dpaToggle').addEventListener('click', function() {
    var content = document.getElementById('dpaContent');
    var isHidden = content.style.display === 'none';
    content.style.display = isHidden ? '' : 'none';
    this.innerHTML = isHidden
        ? '<i class="bi bi-eye-slash me-1"></i>Hide Data Privacy Notice'
        : '<i class="bi bi-eye me-1"></i>View Data Privacy Notice';
});
</script>
@endif
@endsection