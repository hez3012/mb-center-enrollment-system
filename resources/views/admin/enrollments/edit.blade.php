@extends('admin.layouts.app')
@section('title', 'Edit Enrollment')
@section('content')

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
            @csrf @method('PUT')

            {{-- Student info display only --}}
            <div class="alert alert-light border mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Student</small>
                        <strong>{{ $enrollment->student?->list_name }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">School Year</small>
                        <strong>{{ $enrollment->schoolYear?->year_label }}</strong>
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
                            class="form-select @error('program_level_id') is-invalid @enderror" required>
                        @foreach($programLevels as $level)
                            <option value="{{ $level->program_level_id }}"
                                    {{ old('program_level_id', $enrollment->program_level_id) == $level->program_level_id ? 'selected' : '' }}>
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
                           value="{{ old('enrollment_date', $enrollment->enrollment_date?->format('Y-m-d')) }}"
                           required>
                    @error('enrollment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status"
                            class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['pending'=>'Pending Review','pending_payment'=>'Pending Payment','payment_confirmed'=>'Payment Confirmed','enrolled'=>'Enrolled','rejected'=>'Rejected','withdrawn'=>'Withdrawn','completed'=>'Completed'] as $val => $label)
                            <option value="{{ $val }}"
                                    {{ old('status', $enrollment->status) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Remarks <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="remarks"
                           class="form-control @error('remarks') is-invalid @enderror"
                           value="{{ old('remarks', $enrollment->remarks) }}">
                    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Rejection Reason
                        <span class="text-muted small fw-normal">(if rejected)</span>
                    </label>
                    <input type="text" name="rejection_reason"
                           class="form-control @error('rejection_reason') is-invalid @enderror"
                           value="{{ old('rejection_reason', $enrollment->rejection_reason) }}">
                    @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                @foreach($documentTypes as $docType)
                @php
                    $existing = $enrollment->documents
                        ->where('document_type_id', $docType->document_type_id)
                        ->first();
                @endphp
                <div class="border rounded p-3 mb-2 {{ $docType->is_required ? 'border-warning' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-semibold small">{{ $docType->document_name }}</span>
                            @if($docType->is_required)
                                <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">Required</span>
                            @endif
                        </div>
                        <select name="doc_status[{{ $docType->document_type_id }}]"
                                class="form-select form-select-sm" style="width:140px;">
                            @foreach(['pending'=>'Pending','submitted'=>'Submitted','missing'=>'Missing'] as $val => $label)
                                <option value="{{ $val }}"
                                        {{ old("doc_status.{$docType->document_type_id}", $existing?->submission_status ?? 'pending') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
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
                                   accept=".pdf,.jpg,.jpeg,.png">
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
@endsection