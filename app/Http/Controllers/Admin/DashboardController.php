<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers     = User::count();
        $totalStudents  = Student::count();
        $totalGuardians = Guardian::count();
        $activeStudents = Student::where('status', 'active')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalGuardians',
            'activeStudents'
        ));
    }
}