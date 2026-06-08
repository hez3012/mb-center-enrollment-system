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

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
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
                        New Password
                        <span class="text-muted small fw-normal">
                            (leave blank to keep current)
                        </span>
                    </label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select name="role_id" id="roleSelect"
                        class="form-select @error('role_id') is-invalid @enderror" required>
                    @foreach($allowedRoles as $role)
                        <option value="{{ $role->role_id }}"
                                {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                            {{ ucfirst($role->role_name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Permissions
                    <span class="text-muted small fw-normal ms-1">
                        (change role to reset to defaults, or customize manually)
                    </span>
                </label>

                @foreach($permissions->groupBy('category') as $category => $categoryPermissions)
                <div class="border rounded p-3 mb-2 bg-light">
                    <p class="fw-semibold text-primary mb-2 small">
                        <i class="bi bi-folder me-1"></i>{{ $category }}
                    </p>
                    <div class="row g-2">
                        @foreach($categoryPermissions as $permission)
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
                                    <span class="text-muted d-block" style="font-size:11px">
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

{{-- Pass PHP data to JS via hidden HTML element (avoids Intelephense false positives) --}}
<div id="rolePermissionsData"
     data-value='@json($rolePermissions)'
     style="display:none;"></div>

<script>
const rolePermissions = JSON.parse(
    document.getElementById('rolePermissionsData').getAttribute('data-value')
);

document.getElementById('roleSelect').addEventListener('change', function () {
    const selectedRole = parseInt(this.value);
    const defaultPerms = rolePermissions[selectedRole] || [];

    document.querySelectorAll('.permission-check').forEach(checkbox => {
        checkbox.checked = defaultPerms.includes(parseInt(checkbox.value));
    });
});
</script>
@endsection