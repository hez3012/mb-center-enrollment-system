@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Edit User — {{ $user->username }}</h5>
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user->user_id) }}">
            @csrf @method('PUT')

            {{-- Role --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">Role</label>
                @if($userRoleName === 'guardian')
                    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                    <input type="text" class="form-control bg-light" readonly
                           value="Guardian (permanent — cannot be changed)">
                    <small class="text-muted">Guardian accounts cannot be reassigned to other roles.</small>
                @else
                    <select name="role_id" id="roleSelect"
                            class="form-select @error('role_id') is-invalid @enderror" required>
                        @foreach($allowedRoles as $role)
                            <option value="{{ $role->role_id }}"
                                    data-role="{{ $role->role_name }}"
                                    {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                                {{ ucfirst($role->role_name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @endif
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
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name', $user->middle_name) }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light" readonly
                           value="{{ $user->middle_initial }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Birthdate <span class="text-muted small fw-normal">(mm/dd/yyyy)</span>
                    </label>
                    <input type="date" name="birthdate" id="birthdateInput"
                           class="form-control @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate', $user->birthdate?->format('Y-m-d')) }}">
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" id="ageDisplay" class="form-control bg-light" readonly
                           value="{{ $user->age !== null ? $user->age . ' years old' : '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Contact #1</label>
                    <input type="text" name="contact_number_1" id="contact1Input"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1', $user->contact_number_1) }}">
                    @error('contact_number_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Contact #2 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_2"
                           class="form-control @error('contact_number_2') is-invalid @enderror"
                           value="{{ old('contact_number_2', $user->contact_number_2) }}">
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
                        'region'        => old('region',        $user->region ?? ''),
                        'province'      => old('province',      $user->province ?? ''),
                        'city'          => old('city',          $user->city ?? ''),
                        'barangay'      => old('barangay',      $user->barangay ?? ''),
                        'house_unit_no' => old('house_unit_no', $user->house_unit_no ?? ''),
                        'street'        => old('street',        $user->street ?? ''),
                        'zip_code'      => old('zip_code',      $user->zip_code ?? ''),
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
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $user->username) }}" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        New Password <span class="text-muted small fw-normal">(leave blank to keep)</span>
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

            {{-- Guardian-only: Relationship (no Home Address) --}}
            <div id="guardianFields"
                 class="border rounded p-3 mb-4 bg-light {{ $userRoleName === 'guardian' ? '' : 'd-none' }}">
                <p class="fw-semibold text-primary small mb-3">
                    <i class="bi bi-person-heart me-1"></i>Guardian Information
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Relationship to Student</label>
                        <select name="relationship"
                                class="form-select @error('relationship') is-invalid @enderror">
                            <option value="">-- Select --</option>
                            @foreach(['Mother','Father','Grandparent','Aunt/Uncle','Sibling','Legal Guardian','Other'] as $rel)
                                <option value="{{ $rel }}"
                                        {{ old('relationship', $user->guardian?->relationship) == $rel ? 'selected' : '' }}>
                                    {{ $rel }}
                                </option>
                            @endforeach
                        </select>
                        @error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Permissions (hidden for guardian) --}}
            <div id="permissionsSection"
                 class="mb-4 {{ $userRoleName === 'guardian' ? 'd-none' : '' }}">
                <label class="form-label fw-semibold">Permissions</label>
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
                                       {{ $user->permissions->contains('permission_id', $permission->permission_id) ? 'checked' : '' }}>
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
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
    </div>
</div>

{{-- Pass role data via data attributes — keeps script block Blade-free --}}
<div id="editUserMeta"
     data-is-guardian="{{ $userRoleName === 'guardian' ? '1' : '0' }}"
     data-role-permissions='@json($rolePermissions)'
     style="display:none;">
</div>

{{-- Pure JavaScript — zero Blade directives inside --}}
<script>
document.getElementById('middleNameInput').addEventListener('input', function () {
    var mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});

document.getElementById('birthdateInput').addEventListener('change', function () {
    if (!this.value) { document.getElementById('ageDisplay').value = ''; return; }
    var birth = new Date(this.value);
    var today = new Date();
    var age = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() ||
       (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) { age--; }
    document.getElementById('ageDisplay').value = age + ' years old';
});

var meta         = document.getElementById('editUserMeta');
var isGuardian   = meta.dataset.isGuardian === '1';

if (!isGuardian) {
    var rolePermissions    = JSON.parse(meta.dataset.rolePermissions);
    var roleSelect         = document.getElementById('roleSelect');
    var guardianFields     = document.getElementById('guardianFields');
    var permissionsSection = document.getElementById('permissionsSection');

    roleSelect.addEventListener('change', function () {
        var roleName = this.options[this.selectedIndex] ? this.options[this.selectedIndex].getAttribute('data-role') : '';
        var roleId   = parseInt(this.value);

        if (roleName === 'guardian') {
            guardianFields.classList.remove('d-none');
            permissionsSection.classList.add('d-none');
        } else {
            guardianFields.classList.add('d-none');
            permissionsSection.classList.remove('d-none');
            var defaultPerms = rolePermissions[roleId] || [];
            document.querySelectorAll('.permission-check').forEach(function(cb) {
                cb.checked = defaultPerms.includes(parseInt(cb.value));
            });
        }
    });
}
</script>
@endsection