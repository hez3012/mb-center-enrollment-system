<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H.O.P.E. — Guardian Portal</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">H.O.P.E.</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white small">{{ Auth::user()->full_name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h5 class="fw-bold">Guardian Portal</h5>
    <p class="text-muted">Welcome, {{ Auth::user()->full_name }}!</p>
    <div class="alert alert-success">
        Phase 1 complete — Guardian portal is working! ✅
    </div>
</div>

</body>
</html>