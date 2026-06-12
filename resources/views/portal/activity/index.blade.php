@extends('portal.layouts.app')
@section('title', 'My Activity')
@section('content')

<style>
    /* Fix oversized Bootstrap pagination arrows */
    .pagination svg { width: 0.8rem !important; height: 0.8rem !important; }
    .pagination { font-size: 0.875rem; flex-wrap: wrap; }
    .pagination .page-link { padding: 0.3rem 0.6rem; }
</style>

<h5 class="fw-bold mb-4">My Activity</h5>

<ul class="nav nav-tabs mb-3" id="activityTabs">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'log' ? 'active' : '' }}"
           href="{{ route('portal.activity.index', array_merge(request()->query(), ['tab' => 'log'])) }}">
            <i class="bi bi-shield-lock me-1"></i>Login / Logout History
            <span class="badge bg-secondary ms-1">{{ $logs->total() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'trail' ? 'active' : '' }}"
           href="{{ route('portal.activity.index', array_merge(request()->query(), ['tab' => 'trail'])) }}">
            <i class="bi bi-clock-history me-1"></i>Activity Trail
            <span class="badge bg-secondary ms-1">{{ $trails->total() }}</span>
        </a>
    </li>
</ul>

{{-- ── LOG TAB ─────────────────────────────────────────────────────────────── --}}
@if($activeTab === 'log')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                @if($log->action === 'login')
                                    <span class="badge bg-success">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Logged In
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-box-arrow-right me-1"></i>Logged Out
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
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-shield-lock d-block mb-2" style="font-size:1.5rem;"></i>
                                No login / logout history yet.
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
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
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
                            <td>
                                <span class="badge bg-{{ $actionBadge[strtolower($trail->action)] ?? 'secondary' }}">
                                    {{ ucfirst($trail->action) }}
                                </span>
                            </td>
                            <td class="small fw-semibold">{{ $trail->record_label }}</td>
                            <td class="small text-muted" style="max-width:260px;">
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
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-clock-history d-block mb-2" style="font-size:1.5rem;"></i>
                                No activity recorded yet.
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