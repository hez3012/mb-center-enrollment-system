<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user     = Auth::user();
        $guardian = $user->guardian;
        $students = $guardian ? $guardian->students()->with(['programLevel', 'disabilities'])->get() : collect();

        return view('portal.dashboard', compact('guardian', 'students'));
    }
}