@extends('admin.layouts.app')
@section('title', 'Enrollment Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Enrollment Details</h5>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($enrollment->status === 'pending' && $enrollment->enrollment_type === 'online' && Auth::user()->hasPermission('approve_enrollment'))
<div class="alert alert-warning d-flex justify-content-between align-items-center">
    <span>
        <i class="bi bi-hourglass-split me-2"></i>
        This is a <strong>pending online enrollment</strong> waiting for your review.
    </span>
    <div class="d-flex gap-2">
        <form method="POST"
              action="{{ route('admin.enrollments.approve',$enrollment->enrollment_id) }}"
              class="d-inline">
            @csrf
            @method('PATCH')
            <button class="btn btn-sm btn-success">
                <i class="bi bi-check-circle me-1"></i>Approve
            </button>
        </form>
        <button class="btn btn-sm btn-danger"
                data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="bi bi-x-circle me-1"></i>Reject
        </button>
    </div>
</div>
@endif

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-clipboard-check me-1"></i>Enrollment Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Student</td>
                        <td>{{ optional($enrollment->student)->list_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">School Year</td>
                        <td>{{ optional($enrollment->schoolYear)->year_label ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Level</td>
                        <td>{{ optional($enrollment->programLevel)->program_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Enrollment Type</td>
                        <td>
                            <span class="badge bg-{{ $enrollment->enrollment_type === 'walk_in' ? 'secondary' : 'info text-dark' }}">
                                {{ $enrollment->type_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $enrollment->status_badge }}">
                                {{ $enrollment->status_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Enrollment Date</td>
                        <td>{{ $enrollment->enrollment_date?->format('m/d/Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Waiver Signed</td>
                        <td>
                            @if($enrollment->waiver_signed)
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle me-1"></i>No</span>
                            @endif
                        </td>
                    </tr>
                    @if($enrollment->remarks)
                    <tr>
                        <td class="text-muted">Remarks</td>
                        <td>{{ $enrollment->remarks }}</td>
                    </tr>
                    @endif
                    @if($enrollment->rejection_reason)
                    <tr>
                        <td class="text-muted">Rejection Reason</td>
                        <td class="text-danger">{{ $enrollment->rejection_reason }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Processed By</td>
                        <td>{{ optional($enrollment->processedBy)->full_name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date Created</td>
                        <td>{{ $enrollment->created_at?->format('m/d/Y h:i A') ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Student Information
            </div>
            <div class="card-body">
                @if($enrollment->student)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @include('partials.avatar',[
                            'name'  => $enrollment->student->list_name,
                            'image' => $enrollment->student->profile_picture,
                            'size'  => 56,
                        ])
                        <div>
                            <div class="fw-semibold">{{ $enrollment->student->full_name }}</div>
                            <small class="text-muted">{{ $enrollment->student->age }} years old</small>
                        </div>
                    </div>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Guardian</td>
                            <td>{{ optional($enrollment->student->guardian)->full_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Disabilities</td>
                            <td>
                                @forelse($enrollment->student->disabilities as $d)
                                    <span class="badge bg-info text-dark">{{ $d->disability_name }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                        </tr>
                    </table>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-file-earmark-check me-1"></i>Documents
            </div>
            <div class="card-body p-0">
                @forelse($enrollment->documents as $doc)
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">
                                    {{ optional($doc->documentType)->document_name }}
                                </div>
                                @if($doc->notes)
                                    <div class="text-muted small">{{ $doc->notes }}</div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($doc->file_path)
                                    <a href="{{ Storage::url($doc->file_path) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i>
                                    </a>
                                @endif
                                @php $dc=['submitted'=>'success','pending'=>'warning','missing'=>'danger']; @endphp
                                <span class="badge bg-{{ $dc[$doc->submission_status] ?? 'secondary' }}">
                                    {{ ucfirst($doc->submission_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small px-3 py-2 mb-0">No documents on record.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if($enrollment->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger fw-bold">
                    <i class="bi bi-x-circle me-1"></i>Reject Enrollment
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST"
                  action="{{ route('admin.enrollments.reject',$enrollment->enrollment_id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <label class="form-label fw-semibold">Reason for Rejection</label>
                    <textarea name="rejection_reason" rows="3"
                              class="form-control @error('rejection_reason') is-invalid @enderror"
                              placeholder="Explain why this enrollment is being rejected..."
                              required>{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection