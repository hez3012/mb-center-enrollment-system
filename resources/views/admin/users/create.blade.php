@extends('admin.layouts.app')
@section('title', 'Add New User')
@section('content')

@php
function fmtPerm(string $n): string {
    $s = ['walkin' => 'Walk-In', 'ped' => 'Ped.'];
    return implode(' ', array_map(fn($w) => $s[$w] ?? ucfirst($w), explode('_', $n)));
}
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Add New User</h5>
    @if($preselectedRole === 'guardian')
        <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    @else
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Role --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Role <span class="text-danger">*</span>
                </label>
                @if($preselectedRole === 'guardian')
                    @php $guardianRole = $allowedRoles->firstWhere('role_name','guardian'); @endphp
                    <input type="hidden" name="role_id"
                           value="{{ $guardianRole?->role_id }}">
                    <input type="text" class="form-control bg-light" readonly value="Guardian">
                    <small class="text-muted">
                        <i class="bi bi-lock me-1"></i>Role is locked to Guardian.
                    </small>
                @else
                    <select name="role_id" id="roleSelect"
                            class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach($allowedRoles as $role)
                            <option value="{{ $role->role_id }}"
                                    data-role="{{ $role->role_name }}"
                                    {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                                {{ ucfirst($role->role_name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @endif
            </div>

            {{-- Profile Picture --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person-circle me-1"></i>Profile Picture
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div id="avatarWrapper">
                            @include('partials.avatar',['name'=>'?','image'=>null,'size'=>48])
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <label for="profilePicInput" class="btn btn-sm btn-outline-secondary mb-0">
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
                           value="{{ old('first_name') }}" required minlength="2">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name') }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required minlength="2">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light"
                           readonly placeholder="Auto">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Sex <span class="text-danger">*</span>
                    </label>
                    <select name="sex" id="sexSelect"
                            class="form-select @error('sex') is-invalid @enderror" required>
                        <option value="">-- Select --</option>
                        @foreach(['male'=>'Male','female'=>'Female','prefer_not_to_say'=>'Prefer not to say','others'=>'Others'] as $val => $label)
                            <option value="{{ $val }}"
                                    {{ old('sex') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3" id="sexSpecifyWrapper" style="display:none;">
                    <label class="form-label fw-semibold">Please specify</label>
                    <input type="text" name="sex_specify"
                           class="form-control @error('sex_specify') is-invalid @enderror"
                           value="{{ old('sex_specify') }}">
                    @error('sex_specify')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="date" name="birthdate" id="birthdateInput"
                           class="form-control @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate') }}">
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" id="ageDisplay" class="form-control bg-light"
                           readonly placeholder="Auto">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Contact #1 <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="contact_number_1"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1') }}"
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
                           value="{{ old('contact_number_2') }}"
                           maxlength="11">
                    @error('contact_number_2')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <small class="text-muted">Format: 09XXXXXXXXX (11 digits)</small>
                    @enderror
                </div>
            </div>

            {{-- Address --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Address
            </p>
            <div class="mb-4">
                @include('partials.address-fields',[
                    'fieldPrefix' => '',
                    'data' => [
                        'region'        => old('region',''),
                        'province'      => old('province',''),
                        'city'          => old('city',''),
                        'barangay'      => old('barangay',''),
                        'house_unit_no' => old('house_unit_no',''),
                        'street'        => old('street',''),
                        'zip_code'      => old('zip_code',''),
                    ],
                ])
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
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Username <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" required minlength="4">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Password <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required minlength="6">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Minimum 6 characters.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Confirm Password <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password_confirmation"
                           class="form-control" required>
                </div>
            </div>

            {{-- Guardian-only fields --}}
            <div id="guardianFields"
                 class="border rounded p-3 mb-4 bg-light {{ $preselectedRole === 'guardian' ? '' : 'd-none' }}">
                <p class="fw-semibold text-primary small mb-3">
                    <i class="bi bi-person-heart me-1"></i>Guardian Profile Information
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Relationship to Student <span class="text-danger">*</span>
                        </label>
                        <select name="relationship"
                                class="form-select @error('relationship') is-invalid @enderror">
                            <option value="">-- Select --</option>
                            @foreach(['Mother','Father','Grandparent','Aunt/Uncle','Sibling','Legal Guardian','Other'] as $rel)
                                <option value="{{ $rel }}"
                                        {{ old('relationship') === $rel ? 'selected' : '' }}>
                                    {{ $rel }}
                                </option>
                            @endforeach
                        </select>
                        @error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div id="permissionsSection" class="mb-4 d-none">
                <label class="form-label fw-semibold">
                    Permissions
                    <span class="text-muted small fw-normal ms-1">
                        (auto-loaded from role, customizable)
                    </span>
                </label>
                @foreach($permissions->groupBy('category') as $category => $catPerms)
                    <div class="border rounded p-3 mb-2 bg-light">
                        <p class="fw-semibold text-primary mb-2 small">
                            <i class="bi bi-folder me-1"></i>{{ $category }}
                        </p>
                        <div class="row g-2">
                            @foreach($catPerms as $permission)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input permission-check"
                                               type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->permission_id }}"
                                               id="perm_{{ $permission->permission_id }}"
                                               {{ in_array($permission->permission_id, old('permissions',[])) ? 'checked' : '' }}>
                                        <label class="form-check-label small"
                                               for="perm_{{ $permission->permission_id }}">
                                            {{ fmtPerm($permission->permission_name) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Create User
            </button>
        </form>
    </div>
</div>

<div id="createUserMeta"
     data-role-permissions='@json($rolePermissions)'
     data-audit-log-id="{{ $viewAuditLogId }}"
     data-preselected="{{ $preselectedRole }}"
     style="display:none;"></div>

{{-- Fullscreen Modal --}}
<div class="modal fade" id="fullscreenModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 p-1 justify-content-end">
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="fullscreenImg" src="" alt=""
                     class="img-fluid rounded" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>

<script>
var meta             = document.getElementById('createUserMeta');
var rolePermissions  = JSON.parse(meta.dataset.rolePermissions);
var viewAuditLogId   = parseInt(meta.dataset.auditLogId);
var preselectedRole  = meta.dataset.preselected;
var roleSelect       = document.getElementById('roleSelect');
var guardianFields   = document.getElementById('guardianFields');
var permissionsSection = document.getElementById('permissionsSection');

function handleRoleChange() {
    if (preselectedRole === 'guardian') {
        guardianFields.classList.remove('d-none');
        permissionsSection.classList.add('d-none');
        return;
    }

    var opt      = roleSelect ? roleSelect.options[roleSelect.selectedIndex] : null;
    var roleName = opt ? opt.getAttribute('data-role') : '';
    var roleId   = roleSelect ? parseInt(roleSelect.value) : 0;

    if (!roleName) {
        guardianFields.classList.add('d-none');
        permissionsSection.classList.add('d-none');
        return;
    }

    if (roleName === 'guardian') {
        guardianFields.classList.remove('d-none');
        permissionsSection.classList.add('d-none');
    } else {
        guardianFields.classList.add('d-none');
        permissionsSection.classList.remove('d-none');
        var defaultPerms = rolePermissions[roleId] || [];
        document.querySelectorAll('.permission-check').forEach(function(cb) {
            var permId = parseInt(cb.value);
            cb.checked = (permId === viewAuditLogId) ? true : defaultPerms.includes(permId);
        });
    }
}

if (roleSelect) {
    roleSelect.addEventListener('change', handleRoleChange);
}
handleRoleChange();

document.getElementById('middleNameInput').addEventListener('input', function() {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

document.getElementById('birthdateInput').addEventListener('change', function() {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    var birth = new Date(this.value);
    var today = new Date();
    var age   = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) { age--; }
    document.getElementById('ageDisplay').value = age + ' years old';
});

document.getElementById('sexSelect').addEventListener('change', function() {
    document.getElementById('sexSpecifyWrapper').style.display =
        this.value === 'others' ? '' : 'none';
});

// Profile picture preview
document.getElementById('profilePicInput').addEventListener('change', function() {
    var file = this.files[0];
    if (!file) return;
    document.getElementById('picFileName').textContent = file.name;
    var reader = new FileReader();
    reader.onload = function(e) {
        var w = document.getElementById('avatarWrapper');
        w.innerHTML = '<img src="' + e.target.result + '" style="width:48px;height:48px;border-radius:50%;object-fit:cover;cursor:pointer;flex-shrink:0;">';
        w.querySelector('img').addEventListener('click', openFullscreen);
    };
    reader.readAsDataURL(file);
});

function openFullscreen(e) {
    document.getElementById('fullscreenImg').src = e.target.src;
    new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
}
</script>
@endsection