@extends('admin.layouts.app')
@section('title', 'Guardian Management')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Guardian Management</h5>
    @if(Auth::user()->hasPermission('create_user'))
        <a href="{{ route('admin.users.create', ['role' => 'guardian']) }}"
           class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i>Add Guardian via User Management
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                       placeholder="Search by name or email...">
            </div>
            <div class="col-md-3">
                <select id="sortSelect" class="form-select form-select-sm">
                    <option value="az">A–Z Name</option>
                    <option value="za">Z–A Name</option>
                    <option value="created">Date Created</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
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
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Relationship</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guardians as $guardian)
                    @php $user = $guardian->user; @endphp
                    <tr data-name="{{ strtolower(optional($user)->last_name ?? '') }}"
                        data-created="{{ optional($user)->created_at?->timestamp ?? 0 }}"
                        data-status="{{ optional($user)->is_active ? 'active' : 'inactive' }}"
                        data-search="{{ strtolower(optional($user)->list_name.' '.optional($user)->email) }}">
                        <td>{{ $guardian->guardian_id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @include('partials.avatar',[
                                    'name'  => optional($user)->list_name ?? '?',
                                    'image' => optional($user)->profile_picture ?? null,
                                    'size'  => 32,
                                ])
                                <span>{{ optional($user)->list_name ?? '—' }}</span>
                            </div>
                        </td>
                        <td>{{ optional($user)->email ?? '—' }}</td>
                        <td>{{ optional($user)->contact_number_1 ?? '—' }}</td>
                        <td>{{ $guardian->relationship ?? '—' }}</td>
                        <td>{{ $guardian->students->count() }}</td>
                        <td>
                            <span class="badge bg-{{ optional($user)->is_active ? 'success' : 'secondary' }}">
                                {{ optional($user)->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if(Auth::user()->hasPermission('view_guardian'))
                                    <a href="{{ route('admin.guardians.show',$guardian->guardian_id) }}"
                                       class="btn btn-sm btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endif
                                @if(Auth::user()->hasPermission('edit_guardian'))
                                    <a href="{{ route('admin.guardians.edit',$guardian->guardian_id) }}"
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="noDataRow">
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-person-heart d-block mb-2" style="font-size:1.5rem;"></i>
                            No guardians found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="noResults" class="text-center text-muted py-4" style="display:none;">
            <i class="bi bi-search d-block mb-2" style="font-size:1.5rem;"></i>
            No guardians match your search.
        </div>
    </div>
</div>

<script>
var searchInput  = document.getElementById('searchInput');
var sortSelect   = document.getElementById('sortSelect');
var statusFilter = document.getElementById('statusFilter');
var tbody        = document.querySelector('#guardiansTable tbody');
var hasGuardians = tbody.querySelectorAll('tr[data-search]').length > 0;

function applyFilters() {
    if (!hasGuardians) {
        document.getElementById('noResults').style.display = 'none';
        return;
    }

    var search = searchInput.value.toLowerCase().trim();
    var sort   = sortSelect.value;
    var status = statusFilter.value;

    var rows = Array.from(tbody.querySelectorAll('tr[data-search]'));

    rows.forEach(function(row) {
        var show = true;
        if (search && !(row.dataset.search || '').includes(search)) { show = false; }
        if (status && row.dataset.status !== status)                 { show = false; }
        row.style.display = show ? '' : 'none';
    });

    var visible = rows.filter(function(r) { return r.style.display !== 'none'; });
    document.getElementById('noResults').style.display = (visible.length === 0) ? '' : 'none';

    visible.sort(function(a, b) {
        if (sort === 'az')      return (a.dataset.name || '').localeCompare(b.dataset.name || '');
        if (sort === 'za')      return (b.dataset.name || '').localeCompare(a.dataset.name || '');
        if (sort === 'created') return (b.dataset.created || 0) - (a.dataset.created || 0);
        return 0;
    });
    visible.forEach(function(r) { tbody.appendChild(r); });
}

function clearFilters() {
    searchInput.value = '';
    sortSelect.value  = 'az';
    statusFilter.value = '';
    applyFilters();
}

[searchInput, sortSelect, statusFilter].forEach(function(el) {
    el.addEventListener('input', applyFilters);
});

applyFilters();
</script>
@endsection