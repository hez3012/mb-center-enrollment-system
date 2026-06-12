@extends('admin.layouts.app')
@section('title', 'Audit Log')
@section('content')

<style>
    /* Fix oversized Bootstrap pagination arrows */
    .pagination svg { width: 0.8rem !important; height: 0.8rem !important; }
    .pagination { font-size: 0.875rem; flex-wrap: wrap; }
    .pagination .page-link { padding: 0.3rem 0.6rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Audit Log</h5>
    @if($isDirectress)
        <span class="badge bg-primary">Viewing: All Users</span>
    @elseif($isAdmin)
        <span class="badge bg-info text-dark">Viewing: All Users (except Directress)</span>
    @else
        <span class="badge bg-secondary">Viewing: Your Activity Only</span>
    @endif
</div>

<ul class="nav nav-tabs mb-3" id="auditTabs">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'log' ? 'active' : '' }}"
           href="{{ route('admin.audit-log.index', array_merge(request()->query(), ['tab' => 'log'])) }}">
            <i class="bi bi-shield-lock me-1"></i>Log
            <span class="badge bg-secondary ms-1">{{ $logs->total() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'trail' ? 'active' : '' }}"
           href="{{ route('admin.audit-log.index', array_merge(request()->query(), ['tab' => 'trail'])) }}">
            <i class="bi bi-clock-history me-1"></i>Trail
            <span class="badge bg-secondary ms-1">{{ $trails->total() }}</span>
        </a>
    </li>
</ul>

{{-- ── LOG TAB ─────────────────────────────────────────────────────────────── --}}
@if($activeTab === 'log')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET"
                  action="{{ route('admin.audit-log.index') }}"
                  class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="log">
                <div class="col-md-4">
                    <input type="text" name="log_search"
                           class="form-control form-control-sm"
                           placeholder="Search by name..."
                           value="{{ request('log_search') }}">
                </div>
                <div class="col-md-2">
                    <select name="log_action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        <option value="login"  {{ request('log_action') === 'login'  ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('log_action') === 'logout' ? 'selected' : '' }}>Logout</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
                @if(request('log_search') || request('log_action'))
                    <div class="col-auto">
                        <a href="{{ route('admin.audit-log.index', ['tab' => 'log']) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-muted small">{{ $log->auth_log_id }}</td>
                            <td class="fw-semibold small">{{ $log->user?->full_name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border small">
                                    {{ ucfirst($log->user?->role?->role_name ?? '—') }}
                                </span>
                            </td>
                            <td>
                                @if($log->action === 'login')
                                    <span class="badge bg-success">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
                            <td class="small text-muted">
                                {{ $log->logged_at?->format('m/d/Y') }}
                            </td>
                            <td class="small text-muted">
                                {{ $log->logged_at?->format('h:i:s A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-shield-lock d-block mb-2" style="font-size:1.5rem;"></i>
                                No login / logout records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
        <div class="mt-3">{{ $logs->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
@endif

{{-- ── TRAIL TAB ───────────────────────────────────────────────────────────── --}}
@if($activeTab === 'trail')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET"
                  action="{{ route('admin.audit-log.index') }}"
                  class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="trail">
                <div class="col-md-3">
                    <input type="text" name="trail_search"
                           class="form-control form-control-sm"
                           placeholder="Search by name or details..."
                           value="{{ request('trail_search') }}">
                </div>
                <div class="col-md-2">
                    <select name="trail_action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        @foreach(['create','update','delete','approve','reject'] as $act)
                            <option value="{{ $act }}"
                                    {{ request('trail_action') === $act ? 'selected' : '' }}>
                                {{ ucfirst($act) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="trail_table" class="form-select form-select-sm">
                        <option value="">All Modules</option>
                        @foreach(['student','enrollment','users','guardian','payment'] as $tbl)
                            <option value="{{ $tbl }}"
                                    {{ request('trail_table') === $tbl ? 'selected' : '' }}>
                                {{ ucfirst($tbl) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
                @if(request('trail_search') || request('trail_action') || request('trail_table'))
                    <div class="col-auto">
                        <a href="{{ route('admin.audit-log.index', ['tab' => 'trail']) }}"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $actionBadge = [
                            'create'  => 'primary',
                            'update'  => 'warning',
                            'delete'  => 'danger',
                            'approve' => 'success',
                            'reject'  => 'danger',
                        ];
                    @endphp
                    @forelse($trails as $trail)
                        <tr>
                            <td class="text-muted small">{{ $trail->log_id }}</td>
                            <td class="fw-semibold small">{{ $trail->user?->full_name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border small">
                                    {{ ucfirst($trail->user?->role?->role_name ?? '—') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $actionBadge[strtolower($trail->action)] ?? 'secondary' }}">
                                    {{ ucfirst($trail->action) }}
                                </span>
                            </td>
                            <td class="small fw-semibold">{{ $trail->record_label }}</td>
                            <td class="small text-muted" style="max-width:280px;">
                                {{ $trail->formatted_changes }}
                            </td>
                            <td class="small text-muted">
                                {{ $trail->timestamp?->format('m/d/Y') }}
                            </td>
                            <td class="small text-muted">
                                {{ $trail->timestamp?->format('h:i:s A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-clock-history d-block mb-2" style="font-size:1.5rem;"></i>
                                No activity trail records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($trails->hasPages())
        <div class="mt-3">{{ $trails->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
@endif

@endsection