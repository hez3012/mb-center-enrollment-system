@extends('admin.layouts.app')
@section('title', 'Add New Guardian')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Add New Guardian</h5>
    <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="alert alert-info small">
    <i class="bi bi-info-circle me-1"></i>
    Only user accounts with the <strong>Guardian</strong> role that don't have a profile yet are shown below.
    If the list is empty, create a Guardian user account first via
    <a href="{{ route('admin.users.create') }}" class="alert-link">User Management</a>.
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.guardians.store') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Linked User Account</label>
                    <select name="user_id"
                            class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Select Guardian User Account --</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->user_id }}"
                                    {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                                {{ $user->username }} — {{ $user->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" name="contact_number"
                           class="form-control @error('contact_number') is-invalid @enderror"
                           value="{{ old('contact_number') }}" required>
                    @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Relationship to Student</label>
                    <select name="relationship"
                            class="form-select @error('relationship') is-invalid @enderror" required>
                        <option value="">-- Select --</option>
                        @foreach(['Mother', 'Father', 'Grandparent', 'Aunt/Uncle', 'Sibling', 'Legal Guardian', 'Other'] as $rel)
                            <option value="{{ $rel }}"
                                    {{ old('relationship') == $rel ? 'selected' : '' }}>
                                {{ $rel }}
                            </option>
                        @endforeach
                    </select>
                    @error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Address</label>
                    <textarea name="address" rows="2"
                              class="form-control @error('address') is-invalid @enderror"
                              required>{{ old('address') }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Create Guardian
            </button>
        </form>
    </div>
</div>
@endsection