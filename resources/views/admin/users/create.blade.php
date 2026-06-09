@extends('admin.layouts.app')
@section('title', 'Add New User')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Add New User</h5>
    {{-- Back button goes to Guardian Management if coming from there --}}
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
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Role selector first --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">Role</label>
                <select name="role_id" id="roleSelect"
                        class="form-select @error('role_id') is-invalid @enderror" required>
                    <option value="">-- Select Role --</option>
                    @foreach($allowedRoles as $role)
                        <option value="{{ $role->role_id }}"
                                data-role="{{ $role->role_name }}"
                                {{ (old('role_id') == $role->role_id || $preselectedRole === $role->role_name) ? 'selected' : '' }}>
                            {{ ucfirst($role->role_name) }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Personal Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person me-1"></i>Personal Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" required>
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
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light" readonly
                           placeholder="Auto"
                           value="{{ old('middle_name') ? strtoupper(substr(old('middle_name'),0,1)).'.' : '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-muted small fw-normal">(mm/dd/yyyy)</span>
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
                    <label class="form-label fw-semibold" id="contact1Label">Contact #1</label>
                    <input type="text" name="contact_number_1" id="contact1Input"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1') }}">
                    @error('contact_number_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Contact #2 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_2"
                           class="form-control @error('contact_number_2') is-invalid @enderror"
                           value="{{ old('contact_number_2') }}">
                    @error('contact_number_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Address --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Address
            </p>
            <div class="mb-4">
                @include('partials.address-fields', [
                    'fieldPrefix' => '',
                    'data' => [
                        'region'        => old('region', ''),
                        'province'      => old('province', ''),
                        'city'          => old('city', ''),
                        'barangay'      => old('barangay', ''),
                        'house_unit_no' => old('house_unit_no', ''),
                        'street'        => old('street', ''),
                        'zip_code'      => old('zip_code', ''),
                    ],
                    'regions'   => $regions,
                    'provinces' => $provinces,
                    'cities'    => $cities,
                ])
            </div>

            {{-- Account Credentials --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-shield me-1"></i>Account Credentials
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control" required>
                </div>
            </div>

            {{-- Guardian-only: Relationship to Student only (no Home Address) --}}
            <div id="guardianFields" class="border rounded p-3 mb-4 bg-light d-none">
                <p class="fw-semibold text-primary small mb-3">
                    <i class="bi bi-person-heart me-1"></i>Guardian Profile Information
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Relationship to Student</label>
                        <select name="relationship"
                                class="form-select @error('relationship') is-invalid @enderror">
                            <option value="">-- Select --</option>
                            @foreach(['Mother','Father','Grandparent','Aunt/Uncle','Sibling','Legal Guardian','Other'] as $rel)
                                <option value="{{ $rel }}"
                                        {{ old('relationship') == $rel ? 'selected' : '' }}>
                                    {{ $rel }}
                                </option>
                            @endforeach
                        </select>
                        @error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Permissions (hidden for guardian) --}}
            <div id="permissionsSection" class="mb-4">
                <label class="form-label fw-semibold">
                    Permissions
                    <span class="text-muted small fw-normal ms-1">(auto-loaded from role, customizable)</span>
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
                                       {{ in_array($permission->permission_id, old('permissions', [])) ? 'checked' : '' }}>
                                <label class="form-check-label small"
                                       for="perm_{{ $permission->permission_id }}">
                                    {{ $permission->permission_name }}
                                    <span class="text-muted d-block" style="font-size:11px;">
                                        {{ $permission->description }}
                                    </span>
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

<div id="rolePermissionsData" data-value='@json($rolePermissions)' style="display:none;"></div>

<script>
const rolePermissions    = JSON.parse(document.getElementById('rolePermissionsData').getAttribute('data-value'));
const roleSelect         = document.getElementById('roleSelect');
const guardianFields     = document.getElementById('guardianFields');
const permissionsSection = document.getElementById('permissionsSection');
const contact1Input      = document.getElementById('contact1Input');

function handleRoleChange() {
    const opt      = roleSelect.options[roleSelect.selectedIndex];
    const roleName = opt ? opt.getAttribute('data-role') : '';
    const roleId   = parseInt(roleSelect.value);

    if (roleName === 'guardian') {
        guardianFields.classList.remove('d-none');
        permissionsSection.classList.add('d-none');
        contact1Input.required = true;
    } else {
        guardianFields.classList.add('d-none');
        permissionsSection.classList.remove('d-none');
        contact1Input.required = false;
        const defaultPerms = rolePermissions[roleId] || [];
        document.querySelectorAll('.permission-check').forEach(cb => {
            cb.checked = defaultPerms.includes(parseInt(cb.value));
        });
    }
}

roleSelect.addEventListener('change', handleRoleChange);
handleRoleChange();

document.getElementById('middleNameInput').addEventListener('input', function () {
    const mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

document.getElementById('birthdateInput').addEventListener('change', function () {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    const birth = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
    document.getElementById('ageDisplay').value = age + ' years old';
});
</script>
@endsection