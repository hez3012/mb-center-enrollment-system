@extends('portal.layouts.app')
@section('title', 'Profile Settings')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Profile Settings</h5>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('portal.profile.update') }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Profile Picture --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person-circle me-1"></i>Profile Picture
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        @include('partials.avatar', [
                            'name'  => Auth::user()->list_name,
                            'image' => Auth::user()->profile_picture,
                            'size'  => 64,
                        ])
                        <span class="text-muted small">Current picture</span>
                    </div>
                    <input type="file" name="profile_picture"
                           class="form-control @error('profile_picture') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png">
                    @error('profile_picture')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG or PNG only · Max 2MB · Optional</small>
                </div>
            </div>

            {{-- Personal Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person me-1"></i>Personal Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', Auth::user()->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name', Auth::user()->middle_name) }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', Auth::user()->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Contact #1 <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="contact_number_1"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1', Auth::user()->contact_number_1) }}"
                           required>
                    @error('contact_number_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Contact #2 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_2"
                           class="form-control @error('contact_number_2') is-invalid @enderror"
                           value="{{ old('contact_number_2', Auth::user()->contact_number_2) }}">
                    @error('contact_number_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Account Credentials --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-shield me-1"></i>Account Credentials
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', Auth::user()->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Username <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', Auth::user()->username) }}" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        New Password
                        <span class="text-muted small fw-normal">(leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
    </div>
</div>
@endsection