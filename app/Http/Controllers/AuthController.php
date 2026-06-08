<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Detect if input is email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        Log::info('Login attempt', [
            'login'       => $request->login,
            'login_field' => $loginField,
        ]);

        $user = User::where($loginField, $request->login)->first();

        // User not found
        if (!$user) {
            Log::warning('Login failed: user not found', ['login' => $request->login]);
            return back()
                ->withErrors(['login' => 'Invalid email/username or password.'])
                ->withInput();
        }

        // Account inactive
        if (!$user->is_active) {
            Log::warning('Login failed: account inactive', ['login' => $request->login]);
            return back()
                ->withErrors(['login' => 'Your account has been deactivated. Please contact the administrator.'])
                ->withInput();
        }

        // Account locked
        if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
            $minutesLeft = Carbon::now()->diffInMinutes($user->locked_until) + 1;
            Log::warning('Login failed: account locked', ['login' => $request->login]);
            return back()
                ->withErrors(['login' => "Account is temporarily locked. Try again in {$minutesLeft} minute(s)."])
                ->withInput();
        }

        // Attempt login
        if (Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {

            // Reset failed attempts on success
            $user->failed_attempts = 0;
            $user->locked_until    = null;
            $user->save();

            // Log to audit_log
            AuditLog::create([
                'user_id'    => $user->user_id,
                'action'     => 'LOGIN',
                'table_name' => 'users',
                'record_id'  => $user->user_id,
                'changes'    => null,
            ]);

            $request->session()->regenerate();

            Log::info('Login successful', [
                'login' => $request->login,
                'role'  => $user->role->role_name,
            ]);

            // Role-based redirect
            $roleName = $user->role->role_name;

            if (in_array($roleName, ['directress', 'admin', 'teacher', 'staff'])) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('portal.dashboard');
        }

        // Wrong password — increment failed attempts
        $user->failed_attempts += 1;

        // Lock after 5 failed attempts for 15 minutes
        if ($user->failed_attempts >= 5) {
            $user->locked_until    = Carbon::now()->addMinutes(15);
            $user->failed_attempts = 0;
            Log::warning('Account locked: too many failed attempts', ['login' => $request->login]);
        }

        $user->save();

        Log::warning('Login failed: wrong password', [
            'login'           => $request->login,
            'failed_attempts' => $user->failed_attempts,
        ]);

        return back()
            ->withErrors(['login' => 'Invalid email/username or password.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            AuditLog::create([
                'user_id'    => $user->user_id,
                'action'     => 'LOGOUT',
                'table_name' => 'users',
                'record_id'  => $user->user_id,
                'changes'    => null,
            ]);

            Log::info('User logged out', ['user_id' => $user->user_id]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}