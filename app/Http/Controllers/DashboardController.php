<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {

        if (auth()->user()->hasRole('super_admin')) {
            return view('dashboard.admin');
        } else if (auth()->user()->hasRole('guru')) {
            return view('dashboard.guru');
        } else if (auth()->user()->hasRole('siswa')) {
            return view('dashboard.siswa');
        } 
         else if (auth()->user()->hasRole('kepala_produksi')) {
            return view('dashboard.kepala_produksi');
        }
         else if (auth()->user()->hasRole('bendahara')) {
            return view('dashboard.bendahara');
        }
         else if (auth()->user()->hasRole('marketing')) {
            return view('dashboard.marketing');
        }
         else if (auth()->user()->hasRole(roles: 'designer')) {
            return view('dashboard.designer');
        }
         else if (auth()->user()->hasRole('produksi')) {
            return view('dashboard.produksi');
        }else {
            abort(403, 'Unauthorized');
        }

    }
}
