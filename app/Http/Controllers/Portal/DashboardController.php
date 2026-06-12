<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $guardian = $authUser?->guardian;

        $students = collect();

        if ($guardian) {
            $students = Student::where('guardian_id', $guardian->guardian_id)
                ->with(['serviceType', 'disability', 'programLevel'])
                ->get();
        }

        return view('portal.dashboard', compact('guardian', 'students'));
    }
}