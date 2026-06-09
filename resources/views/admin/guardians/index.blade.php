@extends('admin.layouts.app')
@section('title', 'Guardian Management')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Guardian Management</h5>
    @if(Auth::user()->hasPermission('create_user') && !Auth::user()->hasRole('staff'))
    <a href="{{ route('admin.users.create', ['role' => 'guardian']) }}"
       class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i>Add Guardian via User Management
    </a>
    @endif
</div>

<div class="alert alert-info small">
    <i class="bi bi-info-circle me-1"></i>
    Guardian profiles are created automatically when a user with the
    <strong>Guardian</strong> role is added via User Management.
</div>

{{-- Search & Sort Bar --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                       placeholder="Search by name or username...">
            </div>
            <div class="col-md-4">
                <select id="sortSelect" class="form-select form-select-sm">
                    <option value="az">A–Z Name</option>
                    <option value="za">Z–A Name</option>
                    <option value="created">Date Created</option>
                    <option value="modified">Date Modified</option>
                    <option value="students">Number of Students</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-secondary w-100" onclick="clearFilters()">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0" id="guardiansTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Contact #</th>
                    <th>Relationship</th>
                    <th>Username</th>
                    <th>Students</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guardians as $guardian)
                <tr data-name="{{ strtolower($guardian->last_name) }}"
                    data-created="{{ $guardian->created_at?->timestamp ?? 0 }}"
                    data-modified="{{ $guardian->updated_at?->timestamp ?? 0 }}"
                    data-students="{{ $guardian->students->count() }}"
                    data-search="{{ strtolower($guardian->list_name . ' ' . ($guardian->user?->username ?? '')) }}">
                    <td>{{ $guardian->guardian_id }}</td>
                    <td>{{ $guardian->list_name }}</td>
                    <td>{{ $guardian->contact_number }}</td>
                    <td>{{ $guardian->relationship }}</td>
                    <td>{{ $guardian->user?->username ?? '—' }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ $guardian->students->count() }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.guardians.show', $guardian->guardian_id) }}"
                               class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(Auth::user()->hasPermission('edit_guardian'))
                            <a href="{{ route('admin.guardians.edit', $guardian->guardian_id) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">No guardians found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="noResults" class="text-center text-muted py-3" style="display:none;">
            No guardians match your search.
        </div>
    </div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const sortSelect  = document.getElementById('sortSelect');
const tbody       = document.querySelector('#guardiansTable tbody');

function applyFilters() {
    const search = searchInput.value.toLowerCase().trim();
    const sort   = sortSelect.value;
    let rows = Array.from(tbody.querySelectorAll('tr[data-search]'));

    rows.forEach(row => {
        const show = !search || (row.dataset.search || '').includes(search);
        row.style.display = show ? '' : 'none';
    });

    const visible = rows.filter(r => r.style.display !== 'none');
    document.getElementById('noResults').style.display = visible.length === 0 ? '' : 'none';

    visible.sort((a, b) => {
        switch (sort) {
            case 'az':       return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            case 'za':       return (b.dataset.name || '').localeCompare(a.dataset.name || '');
            case 'created':  return (b.dataset.created || 0) - (a.dataset.created || 0);
            case 'modified': return (b.dataset.modified || 0) - (a.dataset.modified || 0);
            case 'students': return parseInt(b.dataset.students || 0) - parseInt(a.dataset.students || 0);
        }
    });
    visible.forEach(r => tbody.appendChild(r));
}

function clearFilters() {
    searchInput.value = '';
    sortSelect.value  = 'az';
    applyFilters();
}

[searchInput, sortSelect].forEach(el => el.addEventListener('input', applyFilters));
applyFilters();
</script>
@endsection