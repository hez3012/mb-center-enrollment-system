<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H.O.P.E. Portal — @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            margin: 2px 8px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover { background-color: #f1f3f5; color: #212529; }
        .sidebar .nav-link.active { background-color: #e7f1ff; color: #0d6efd; font-weight: 500; }
        .main-wrapper {
            margin-left: 240px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .content-area { padding: 1.5rem; flex: 1; }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="fw-bold text-primary" style="font-size:1rem;">
            <i class="bi bi-heart-pulse me-2"></i>H.O.P.E.
        </div>
        <div class="text-muted" style="font-size:0.7rem;">Guardian Portal</div>
    </div>

    <nav class="nav flex-column pt-2">
        <a href="{{ route('portal.dashboard') }}"
           class="nav-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="{{ route('portal.enrollments.index') }}"
           class="nav-link {{ request()->routeIs('portal.enrollments.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check me-2"></i>My Enrollments
        </a>
    </nav>

    <div class="mt-auto p-3 border-top">
        <div class="small text-muted mb-1">
            <i class="bi bi-person-circle me-1"></i>
            {{ Auth::user()->full_name ?? Auth::user()->username }}
        </div>
        <div class="small text-muted mb-2">
            <span class="badge bg-secondary">Guardian</span>
        </div>
        <a href="{{ route('portal.profile.edit') }}"
           class="btn btn-sm btn-outline-secondary w-100 mb-1">
            <i class="bi bi-gear me-1"></i>Profile Settings
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</div>

{{-- Main Wrapper --}}
<div class="main-wrapper">

    {{-- Topbar --}}
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="fw-semibold text-dark">@yield('title', 'Dashboard')</div>
        <div class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i>
            {{ now()->format('F d, Y') }}
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="content-area">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>