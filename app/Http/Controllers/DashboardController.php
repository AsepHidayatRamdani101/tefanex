<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }


        if ($user->hasRole('super_admin')) {
            return view('dashboard.admin');
        } else if ($user->hasRole('guru')) {
            return view('dashboard.guru');
        } else if ($user->hasRole('siswa')) {
            return view('dashboard.siswa');
        } else if ($user->hasRole('kepala_tefa')) {
            return view('dashboard.kepala_tefa');
        } else if ($user->hasRole('bendahara')) {
            return view('dashboard.bendahara');
        } else if ($user->hasRole('marketing')) {
            return view('dashboard.marketing');
        } else if ($user->hasRole(roles: 'designer')) {
            return view('dashboard.designer');
        } else if ($user->hasRole('produksi')) {
            return view('dashboard.produksi');
        } else {
            abort(403, 'Unauthorized');
        }
    }
}
