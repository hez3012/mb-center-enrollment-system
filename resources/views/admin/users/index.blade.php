@extends('admin.layouts.app')
@section('title', 'User Management')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">User Management</h5>
    @if(Auth::user()->hasPermission('create_user'))
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Add New User
    </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->user_id }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $user->role->role_name }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        {{-- Hide action buttons for own account --}}
                        @if(Auth::user()->user_id !== $user->user_id)
                            @if(Auth::user()->hasPermission('edit_user'))
                            <a href="{{ route('admin.users.edit', $user->user_id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                            @if(Auth::user()->hasPermission('deactivate_user'))
                            <form method="POST"
                                  action="{{ route('admin.users.toggle', $user->user_id) }}"
                                  class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-{{ $user->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                </button>
                            </form>
                            @endif
                        @else
                            <span class="text-muted small fst-italic">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection