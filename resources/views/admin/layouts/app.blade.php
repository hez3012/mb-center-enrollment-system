<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H.O.P.E. — @yield('title')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>

<nav class="navbar navbar-dark bg-primary px-3">
    <span class="navbar-brand fw-bold">H.O.P.E.</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="text-white small">
            {{ Auth::user()->full_name }}
            <span class="badge bg-light text-primary ms-1">
                {{ Auth::user()->role->role_name }}
            </span>
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</nav>

<div class="d-flex" style="min-height: calc(100vh - 56px);">

    <div class="bg-dark text-white d-flex flex-column p-3" style="width: 220px; min-width: 220px;">

        <!-- Main navigation -->
        <ul class="nav flex-column gap-1 flex-grow-1">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'bg-primary rounded' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>

            @if(Auth::user()->hasPermission('view_user') ||
                Auth::user()->hasPermission('create_user') ||
                Auth::user()->hasPermission('edit_user') ||
                Auth::user()->hasPermission('deactivate_user'))
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}"
                   class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'bg-primary rounded' : '' }}">
                    <i class="bi bi-people me-2"></i>User Management
                </a>
            </li>
            @endif
        </ul>

        <!-- Profile Settings pinned at bottom -->
        <div class="border-top border-secondary pt-2 mt-2">
            <a href="{{ route('admin.profile') }}"
               class="nav-link text-white {{ request()->routeIs('admin.profile') ? 'bg-primary rounded' : '' }}">
                <i class="bi bi-person-gear me-2"></i>Profile Settings
            </a>
        </div>

    </div>

    <div class="flex-grow-1 p-4 bg-light">

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

</div>

</body>
</html>