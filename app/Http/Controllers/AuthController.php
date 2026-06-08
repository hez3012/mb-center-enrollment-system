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
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        Log::info('Login attempt', ['username' => $request->username]);

        $user = User::where('username', $request->username)->first();

        // User not found
        if (!$user) {
            Log::warning('Login failed: user not found', ['username' => $request->username]);
            return back()
                ->withErrors(['username' => 'Invalid username or password.'])
                ->withInput();
        }

        // Account inactive
        if (!$user->is_active) {
            Log::warning('Login failed: account inactive', ['username' => $request->username]);
            return back()
                ->withErrors(['username' => 'Your account has been deactivated. Please contact the administrator.'])
                ->withInput();
        }

        // Account locked
        if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
            $minutesLeft = Carbon::now()->diffInMinutes($user->locked_until) + 1;
            Log::warning('Login failed: account locked', ['username' => $request->username]);
            return back()
                ->withErrors(['username' => "Account is temporarily locked. Try again in {$minutesLeft} minute(s)."])
                ->withInput();
        }

        // Attempt login
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {

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
                'username' => $user->username,
                'role'     => $user->role->role_name
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
            Log::warning('Account locked: too many failed attempts', ['username' => $request->username]);
        }

        $user->save();

        Log::warning('Login failed: wrong password', [
            'username'        => $request->username,
            'failed_attempts' => $user->failed_attempts,
        ]);

        return back()
            ->withErrors(['username' => 'Invalid username or password.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::user()->user_id;

            AuditLog::create([
                'user_id'    => $userId,
                'action'     => 'LOGOUT',
                'table_name' => 'users',
                'record_id'  => $userId,
                'changes'    => null,
            ]);

            Log::info('User logged out', ['user_id' => $userId]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}