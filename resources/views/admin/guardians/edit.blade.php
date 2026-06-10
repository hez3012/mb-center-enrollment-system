@extends('admin.layouts.app')
@section('title', 'Edit Guardian')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Edit Guardian</h5>
    <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        {{-- Profile picture display (non-editable) --}}
        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light border rounded">
            @include('partials.avatar',[
                'name'  => optional($guardian->user)->list_name ?? '?',
                'image' => optional($guardian->user)->profile_picture ?? null,
                'size'  => 56,
            ])
            <div>
                <div class="fw-semibold">{{ optional($guardian->user)->list_name }}</div>
                <small class="text-muted">
                    Profile picture can be changed via
                    <a href="{{ route('admin.users.edit', $guardian->user->user_id) }}">
                        User Management
                    </a>.
                </small>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.guardians.update',$guardian->guardian_id) }}">
            @csrf
            @method('PUT')

            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person-heart me-1"></i>Guardian Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Relationship to Student <span class="text-danger">*</span>
                    </label>
                    <select name="relationship"
                            class="form-select @error('relationship') is-invalid @enderror"
                            required>
                        <option value="">-- Select --</option>
                        @foreach(['Mother','Father','Grandparent','Aunt/Uncle','Sibling','Legal Guardian','Other'] as $rel)
                            <option value="{{ $rel }}"
                                    {{ old('relationship',$guardian->relationship) == $rel ? 'selected' : '' }}>
                                {{ $rel }}
                            </option>
                        @endforeach
                    </select>
                    @error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Contact #1 <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="contact_number_1"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1', optional($guardian->user)->contact_number_1) }}"
                           required>
                    @error('contact_number_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Contact #2 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_2"
                           class="form-control @error('contact_number_2') is-invalid @enderror"
                           value="{{ old('contact_number_2', optional($guardian->user)->contact_number_2) }}">
                    @error('contact_number_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="alert alert-info small">
                <i class="bi bi-info-circle me-1"></i>
                To edit the guardian's name, address, email, username, or password — use
                <a href="{{ route('admin.users.edit', $guardian->user->user_id) }}" class="fw-semibold">
                    User Management
                </a>.
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
    </div>
</div>
@endsection