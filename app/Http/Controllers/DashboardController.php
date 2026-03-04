<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalUsers = \App\Models\User::count();
        $totalRoles = \Spatie\Permission\Models\Role::count();
        

        return view('dashboard', compact('user', 'totalUsers', 'totalRoles'));
    }
}
