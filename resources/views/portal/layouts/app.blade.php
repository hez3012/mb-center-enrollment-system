<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guardian Portal') — H.O.P.E.</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e9ecef;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar .brand {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            margin: 2px 8px;
            font-size: 0.9rem;
            transition: background-color 0.15s;
        }
        .sidebar .nav-link:hover  { background-color: #f1f3f5; color: #212529; }
        .sidebar .nav-link.active { background-color: #e7f1ff; color: #0d6efd; font-weight: 500; }
        .sidebar .nav-link i { width: 20px; }
        .sidebar-bottom {
            margin-top: auto;
            padding: 0.5rem 0;
            border-top: 1px solid #e9ecef;
        }
        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
            min-height: 100vh;
        }
        .topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.6rem 1.5rem;
            margin-left: 250px;
            position: sticky;
            top: 0;
            z-index: 99;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .role-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar">
    <div class="brand">
        <div class="fw-bold text-primary" style="font-size:1rem;">H.O.P.E.</div>
        <div class="text-muted" style="font-size:0.75rem;">Guardian Portal</div>
    </div>

    <nav class="nav flex-column pt-2">
        <a href="{{ route('portal.dashboard') }}"
           class="nav-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('portal.profile.edit') }}"
           class="nav-link {{ request()->routeIs('portal.profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear me-2"></i>Profile Settings
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="nav-link border-0 bg-transparent w-100 text-start text-danger">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </button>
        </form>
    </div>
</div>

{{-- Top bar --}}
<div class="topbar">
    <span class="fw-semibold text-muted small">@yield('title', 'Dashboard')</span>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">{{ Auth::user()->full_name }}</span>
        <span class="badge bg-secondary role-badge">Guardian</span>
    </div>
</div>

{{-- Main Content --}}
<div class="main-content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            <i class="bi bi-info-circle me-1"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>