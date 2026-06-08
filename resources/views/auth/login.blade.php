<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H.O.P.E. — Login</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">

            <!-- Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">

                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-primary">H.O.P.E.</h4>
                        <p class="text-muted small mb-0">
                            Holistic Online Profile and Enrollment System
                        </p>
                        <p class="text-muted small">M.B. Therapy Center</p>
                    </div>

                    <!-- Error messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input
                                    type="text"
                                    name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username') }}"
                                    placeholder="Enter your username"
                                    autofocus
                                    required
                                >
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter your password"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Login
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <!-- Footer note -->
            <p class="text-center text-muted small mt-3">
                &copy; {{ date('Y') }} M.B. Therapy Center. All rights reserved.
            </p>

        </div>
    </div>
</div>

</body>
</html>