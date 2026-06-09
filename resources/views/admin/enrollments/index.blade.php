@extends('admin.layouts.app')
@section('title', 'Enrollment Management')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Enrollment Management</h5>
    @if(Auth::user()->hasPermission('create_enrollment'))
    <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Add Walk-in Enrollment
    </a>
    @endif
</div>

{{-- Search & Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                       placeholder="Search by student name...">
            </div>
            <div class="col-md-2">
                <select id="yearFilter" class="form-select form-select-sm">
                    <option value="">All School Years</option>
                    @foreach($schoolYears as $sy)
                        <option value="{{ $sy->school_year_id }}">{{ $sy->year_label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="enrolled">Enrolled</option>
                    <option value="rejected">Rejected</option>
                    <option value="withdrawn">Withdrawn</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="typeFilter" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="walk_in">Walk-in</option>
                    <option value="online">Online</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="sortSelect" class="form-select form-select-sm">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="az">A–Z Name</option>
                    <option value="za">Z–A Name</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-sm btn-outline-secondary w-100"
                        onclick="clearFilters()" title="Clear">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0" id="enrollmentsTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>School Year</th>
                    <th>Program</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                <tr data-name="{{ strtolower($enrollment->student?->last_name ?? '') }}"
                    data-created="{{ $enrollment->created_at?->timestamp ?? 0 }}"
                    data-year="{{ $enrollment->school_year_id }}"
                    data-status="{{ $enrollment->status }}"
                    data-type="{{ $enrollment->enrollment_type }}"
                    data-search="{{ strtolower($enrollment->student?->list_name ?? '') }}">
                    <td>{{ $enrollment->enrollment_id }}</td>
                    <td>{{ $enrollment->student?->list_name ?? '—' }}</td>
                    <td>{{ $enrollment->schoolYear?->year_label ?? '—' }}</td>
                    <td>{{ $enrollment->programLevel?->program_name ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $enrollment->enrollment_type === 'walk_in' ? 'secondary' : 'info text-dark' }}">
                            {{ $enrollment->type_label }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $enrollment->status_badge }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </td>
                    <td>{{ $enrollment->enrollment_date?->format('m/d/Y') ?? '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.enrollments.show', $enrollment->enrollment_id) }}"
                               class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(Auth::user()->hasPermission('edit_enrollment'))
                            <a href="{{ route('admin.enrollments.edit', $enrollment->enrollment_id) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                            @if(Auth::user()->hasPermission('delete_enrollment'))
                            <button class="btn btn-sm btn-outline-danger" title="Delete"
                                    data-id="{{ $enrollment->enrollment_id }}"
                                    onclick="confirmDelete(this.dataset.id)">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">No enrollments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="noResults" class="text-center text-muted py-3" style="display:none;">
            No enrollments match your search.
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger fw-bold">
                    <i class="bi bi-trash me-1"></i>Delete Enrollment
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small text-muted">
                Are you sure you want to delete this enrollment record?
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var searchInput  = document.getElementById('searchInput');
var yearFilter   = document.getElementById('yearFilter');
var statusFilter = document.getElementById('statusFilter');
var typeFilter   = document.getElementById('typeFilter');
var sortSelect   = document.getElementById('sortSelect');
var tbody        = document.querySelector('#enrollmentsTable tbody');

function applyFilters() {
    var search = searchInput.value.toLowerCase().trim();
    var year   = yearFilter.value;
    var status = statusFilter.value;
    var type   = typeFilter.value;
    var sort   = sortSelect.value;

    var rows = Array.from(tbody.querySelectorAll('tr[data-search]'));

    rows.forEach(function(row) {
        var show = true;
        if (search && !(row.dataset.search || '').includes(search)) { show = false; }
        if (year   && row.dataset.year   !== year)                  { show = false; }
        if (status && row.dataset.status !== status)                { show = false; }
        if (type   && row.dataset.type   !== type)                  { show = false; }
        row.style.display = show ? '' : 'none';
    });

    var visible = rows.filter(function(r) { return r.style.display !== 'none'; });
    document.getElementById('noResults').style.display = visible.length === 0 ? '' : 'none';

    visible.sort(function(a, b) {
        if (sort === 'newest')  return (b.dataset.created || 0) - (a.dataset.created || 0);
        if (sort === 'oldest')  return (a.dataset.created || 0) - (b.dataset.created || 0);
        if (sort === 'az')      return (a.dataset.name || '').localeCompare(b.dataset.name || '');
        if (sort === 'za')      return (b.dataset.name || '').localeCompare(a.dataset.name || '');
        return 0;
    });
    visible.forEach(function(r) { tbody.appendChild(r); });
}

function clearFilters() {
    searchInput.value  = '';
    yearFilter.value   = '';
    statusFilter.value = '';
    typeFilter.value   = '';
    sortSelect.value   = 'newest';
    applyFilters();
}

function confirmDelete(id) {
    document.getElementById('deleteForm').action = '/admin/enrollments/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

[searchInput, yearFilter, statusFilter, typeFilter, sortSelect].forEach(function(el) {
    el.addEventListener('input', applyFilters);
});
applyFilters();
</script>
@endsection