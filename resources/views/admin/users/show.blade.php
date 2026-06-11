@extends('admin.layouts.app')
@section('title', 'User Details')
@section('content')

@php
function fmtPerm(string $n): string {
    $s = ['walkin' => 'Walk-In', 'ped' => 'Ped.'];
    return implode(' ', array_map(fn($w) => $s[$w] ?? ucfirst($w), explode('_', $n)));
}
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">User Details</h5>
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-3">
    {{-- Left Panel --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <div id="avatarWrapper" style="cursor:pointer;display:inline-block;">
                    @include('partials.avatar',[
                        'name'  => $user->list_name,
                        'image' => $user->profile_picture,
                        'size'  => 80,
                    ])
                </div>
                <div class="fw-bold mt-3">{{ $user->list_name }}</div>
                @php
                    $roleColors = [
                        'directress' => 'danger',
                        'admin'      => 'primary',
                        'teacher'    => 'success',
                        'staff'      => 'info',
                        'guardian'   => 'secondary',
                    ];
                @endphp
                <span class="badge bg-{{ $roleColors[$user->role?->role_name] ?? 'secondary' }} mt-1">
                    {{ ucfirst($user->role?->role_name ?? 'N/A') }}
                </span>
                <br>
                <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }} mt-1">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Personal Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    @php $na = 'N/A'; @endphp
                    <tr>
                        <td class="text-muted" style="width:35%">Full Name</td>
                        <td>{{ $user->full_name ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sex</td>
                        <td>{{ $user->sex_display ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $user->email ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Username</td>
                        <td>{{ $user->username ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ $user->birthdate?->format('m/d/Y') ?? $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Age</td>
                        <td>{{ $user->age !== null ? $user->age . ' years old' : $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #1</td>
                        <td>{{ $user->contact_number_1 ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #2</td>
                        <td>{{ $user->contact_number_2 ?: $na }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ $user->full_address ?: $na }}</td>
                    </tr>
                    @if($user->role?->role_name === 'guardian' && $user->guardian)
                    <tr>
                        <td class="text-muted">Relationship</td>
                        <td>{{ $user->guardian->relationship ?: $na }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        @if($user->role?->role_name !== 'guardian')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-shield-check me-1"></i>Permissions
            </div>
            <div class="card-body">
                @foreach($allPermissions->groupBy('category') as $category => $catPerms)
                    <div class="mb-3">
                        <p class="fw-semibold small text-primary mb-2">
                            <i class="bi bi-folder me-1"></i>{{ $category }}
                        </p>
                        <div class="row g-1">
                            @foreach($catPerms as $perm)
                                @php $hasIt = $user->permissions->contains('permission_id', $perm->permission_id); @endphp
                                <div class="col-md-6 small d-flex align-items-center gap-1">
                                    <i class="bi bi-{{ $hasIt ? 'check-circle-fill text-success' : 'x-circle text-danger' }}"></i>
                                    <span>{{ fmtPerm($perm->permission_name) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
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
(function() {
    var w   = document.getElementById('avatarWrapper');
    var img = w ? w.querySelector('img') : null;
    if (img) {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            document.getElementById('fullscreenImg').src = this.src;
            new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
        });
    }
})();
</script>
@endsection