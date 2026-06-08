<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role->role_name ?? null;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}