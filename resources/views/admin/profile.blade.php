@extends('admin.layouts.app')
@section('title', 'Profile Settings')
@section('content')

<h5 class="fw-bold mb-3">
    <i class="bi bi-person-gear me-2"></i>Profile Settings
</h5>

{{-- Error message --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Update profile form --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        Account Information
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $user->username) }}" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        New Password
                        <span class="text-muted small fw-normal">
                            (leave blank to keep current)
                        </span>
                    </label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
    </div>
</div>

{{-- Danger Zone --}}
<div class="card border-danger">
    <div class="card-header text-danger fw-semibold bg-white">
        <i class="bi bi-exclamation-triangle me-1"></i>Danger Zone
    </div>
    <div class="card-body">
        @if(Auth::user()->role->role_name !== 'directress')
            <p class="text-muted small mb-3">
                Deactivating your account will log you out immediately.
                You will need an administrator to reactivate it before you can log in again.
            </p>
            <button type="button"
                    class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#deactivateModal">
                <i class="bi bi-person-x me-1"></i>Deactivate My Account
            </button>
        @else
            <p class="text-muted small mb-0 fst-italic">
                <i class="bi bi-lock me-1"></i>
                The Directress account cannot be self-deactivated for system security reasons.
            </p>
        @endif
    </div>
</div>

{{-- Deactivate Confirmation Modal --}}
@if(Auth::user()->role->role_name !== 'directress')
<div class="modal fade" id="deactivateModal" tabindex="-1"
     aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger fw-bold" id="deactivateModalLabel">
                    <i class="bi bi-person-x me-1"></i>Deactivate Account
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">
                    Are you sure you want to deactivate your account?
                    You will be <strong>logged out immediately</strong> and will need
                    an administrator to reactivate your account.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button"
                        class="btn btn-sm btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <form method="POST" action="{{ route('admin.profile.deactivate') }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-person-x me-1"></i>Yes, Deactivate
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection