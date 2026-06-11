@extends('portal.layouts.app')
@section('title', 'Profile Settings')
@section('content')

@php
    /** @var \App\Models\User $me */
    $me     = auth()->user();
    $meName = trim($me->first_name . ' ' . $me->last_name);
    $meMI   = $me->middle_initial;
@endphp

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
                        <div id="avatarWrapper" style="cursor:pointer;">
                            @include('partials.avatar',[
                                'name'  => $meName ?: '?',
                                'image' => $me->profile_picture,
                                'size'  => 64,
                            ])
                        </div>
                        <span class="text-muted small">Current picture</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <label for="profilePicInput"
                               class="btn btn-sm btn-outline-secondary mb-0">
                            <i class="bi bi-image me-1"></i>Choose Picture
                        </label>
                        <input type="file" name="profile_picture" id="profilePicInput"
                               class="d-none @error('profile_picture') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png">
                        <span id="picFileName" class="text-muted small">No file chosen</span>
                    </div>
                    @error('profile_picture')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG or PNG only · Max 50MB · Optional</small>
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
                           value="{{ old('first_name', $me->first_name) }}"
                           required minlength="2">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name', $me->middle_name) }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $me->last_name) }}"
                           required minlength="2">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light"
                           readonly value="{{ $meMI }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Contact #1 <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="contact_number_1"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1', $me->contact_number_1) }}"
                           maxlength="11" required>
                    @error('contact_number_1')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <small class="text-muted">Format: 09XXXXXXXXX (11 digits)</small>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Contact #2 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_2"
                           class="form-control @error('contact_number_2') is-invalid @enderror"
                           value="{{ old('contact_number_2', $me->contact_number_2) }}"
                           maxlength="11">
                    @error('contact_number_2')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <small class="text-muted">Format: 09XXXXXXXXX (11 digits)</small>
                    @enderror
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
                           value="{{ old('email', $me->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Username <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $me->username) }}"
                           required minlength="4">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        New Password
                        <span class="text-muted small fw-normal">(leave blank to keep)</span>
                    </label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           minlength="6">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Minimum 6 characters.</small>
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

{{-- Fullscreen Modal --}}
<div class="modal fade" id="fullscreenModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 p-1 justify-content-end">
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="fullscreenImg" src="" alt="Profile Picture"
                     class="img-fluid rounded" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('middleNameInput').addEventListener('input', function() {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

// Profile picture live preview
document.getElementById('profilePicInput').addEventListener('change', function() {
    var file = this.files[0];
    if (!file) return;
    document.getElementById('picFileName').textContent = file.name;
    var reader = new FileReader();
    reader.onload = function(e) {
        var w = document.getElementById('avatarWrapper');
        w.innerHTML = '<img src="' + e.target.result
            + '" class="av-64 rounded-circle" style="cursor:pointer;object-fit:cover;">';
        w.querySelector('img').addEventListener('click', openFullscreen);
    };
    reader.readAsDataURL(file);
});

// Fullscreen for existing photo
(function() {
    var w   = document.getElementById('avatarWrapper');
    var img = w ? w.querySelector('img') : null;
    if (img) {
        img.style.cursor = 'pointer';
        img.addEventListener('click', openFullscreen);
    }
})();

function openFullscreen(e) {
    document.getElementById('fullscreenImg').src = e.target.src;
    new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
}
</script>
@endsection