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
                    <input type="text" class="form-control bg-light" readonly
                           value="{{ $currentYear?->year_label ?? 'No active school year' }}">
                    <input type="hidden" name="school_year_id"
                           value="{{ $currentYear?->school_year_id }}">
                    <input type="hidden" name="program_level_id" id="programLevelHidden"
                           value="{{ old('program_level_id') }}">
                </div>
            </div>

            {{-- Documents --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-file-earmark-check me-1"></i>Required Documents
            </p>
            <div class="border rounded p-3 mb-4">
                <p class="text-muted small mb-3">
                    Upload each document as a PDF or image file (max 5MB each).
                    Required documents are marked with
                    <span class="badge bg-warning text-dark">Required</span>.
                    Optional documents can be submitted later.
                </p>
                @foreach($documentTypes as $docType)
                <div class="border rounded p-3 mb-2 {{ $docType->is_required ? 'border-warning' : '' }}">
                    <div class="mb-1">
                        <span class="fw-semibold small">{{ $docType->type_name }}</span>
                        @if($docType->is_required)
                            <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">Required</span>
                        @else
                            <span class="badge bg-light text-muted ms-1 border" style="font-size:10px;">Optional</span>
                        @endif
                        <div class="text-muted small">{{ $docType->description }}</div>
                    </div>
                    <input type="file"
                           name="doc_file[{{ $docType->document_type_id }}]"
                           class="form-control form-control-sm"
                           accept=".pdf,.jpg,.jpeg,.png">
                </div>
                @endforeach
            </div>

            {{-- Waiver --}}
            <div class="border rounded p-3 mb-4 bg-light">
                <p class="fw-semibold small mb-2">
                    <i class="bi bi-file-text me-1"></i>Parent / Guardian Waiver
                </p>
                <p class="text-muted small mb-3">
                    By checking the box below, you confirm that all information provided
                    is accurate and complete, and you agree to M.B. Therapy Center's
                    enrollment terms and conditions.
                </p>
                <div class="form-check">
                    <input class="form-check-input @error('waiver_signed') is-invalid @enderror"
                           type="checkbox" name="waiver_signed" id="waiverCheck" value="1"
                           {{ old('waiver_signed') ? 'checked' : '' }} required>
                    <label class="form-check-label fw-semibold small" for="waiverCheck">
                        I agree to the enrollment terms and confirm all information is correct.
                    </label>
                    @error('waiver_signed')
                        <div class="invalid-feedback">You must accept the waiver to proceed.</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Submit Enrollment
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('studentSelect').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    document.getElementById('programLevelHidden').value = opt ? (opt.dataset.program || '') : '';
});
</script>
@endif
@endsection