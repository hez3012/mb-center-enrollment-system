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
                    <label class="form-label fw-semibold">Student</label>
                    <select name="student_id" id="studentSelect"
                            class="form-select @error('student_id') is-invalid @enderror" required>
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
                    <label class="form-label fw-semibold">School Year</label>
                    <select name="school_year_id"
                            class="form-select @error('school_year_id') is-invalid @enderror" required>
                        <option value="">-- Select School Year --</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy->school_year_id }}"
                                    {{ (old('school_year_id') == $sy->school_year_id || ($currentYear && $currentYear->school_year_id == $sy->school_year_id)) ? 'selected' : '' }}>
                                {{ $sy->year_label }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Program Level</label>
                    <select name="program_level_id" id="programLevelSelect"
                            class="form-select @error('program_level_id') is-invalid @enderror" required>
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
                    <label class="form-label fw-semibold">Enrollment Date</label>
                    <input type="date" name="enrollment_date"
                           class="form-control @error('enrollment_date') is-invalid @enderror"
                           value="{{ old('enrollment_date', now()->format('Y-m-d')) }}" required>
                    @error('enrollment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status"
                            class="form-select @error('status') is-invalid @enderror" required>
                        <option value="enrolled"         {{ old('status','enrolled') === 'enrolled'         ? 'selected' : '' }}>Enrolled</option>
                        <option value="pending_payment"  {{ old('status') === 'pending_payment'             ? 'selected' : '' }}>Pending Payment</option>
                        <option value="pending"          {{ old('status') === 'pending'                     ? 'selected' : '' }}>Pending Review</option>
                        <option value="withdrawn"        {{ old('status') === 'withdrawn'                   ? 'selected' : '' }}>Withdrawn</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    Track submission status for each document. Optionally upload a scanned copy.
                </p>
                @foreach($documentTypes as $docType)
                <div class="border rounded p-3 mb-2 {{ $docType->is_required ? 'border-warning' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-semibold small">{{ $docType->document_name }}</span>
                            @if($docType->is_required)
                                <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">Required</span>
                            @else
                                <span class="badge bg-light text-muted ms-1 border" style="font-size:10px;">Optional</span>
                            @endif
                        </div>
                        <select name="doc_status[{{ $docType->document_type_id }}]"
                                class="form-select form-select-sm" style="width:140px;">
                            <option value="pending"   {{ old("doc_status.{$docType->document_type_id}", 'pending') === 'pending'    ? 'selected' : '' }}>Pending</option>
                            <option value="submitted" {{ old("doc_status.{$docType->document_type_id}") === 'submitted'             ? 'selected' : '' }}>Submitted</option>
                            <option value="missing"   {{ old("doc_status.{$docType->document_type_id}") === 'missing'               ? 'selected' : '' }}>Missing</option>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Upload (optional)</label>
                            <input type="file"
                                   name="doc_file[{{ $docType->document_type_id }}]"
                                   class="form-control form-control-sm"
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
var studentSelect  = document.getElementById('studentSelect');
var programSelect  = document.getElementById('programLevelSelect');

studentSelect.addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var programId = opt ? opt.dataset.program : '';
    if (programId) {
        Array.from(programSelect.options).forEach(function(o) {
            o.selected = (o.value === programId);
        });
    }
});
</script>
@endsection