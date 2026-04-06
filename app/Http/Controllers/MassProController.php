<?php

namespace App\Http\Controllers;

use App\Models\Mass_Production;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MassProController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('masspro.index');
    }

    public function data()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $massPro = Project::leftJoin('mass_productions', 'projects.id', '=', 'mass_productions.project_id')
            ->join('design_briefs', 'projects.id', '=', 'design_briefs.project_id')
            ->join('timelines', 'projects.id', '=', 'timelines.project_id')
            ->where('design_briefs.approval_status', 'approved')
            ->select(
                'projects.judul as project_nama',
                'mass_productions.id as id',
                'mass_productions.quantity as quantity',
                'mass_productions.status as status',
                'timelines.end_date as waktu'
            )
            ->get();

        return DataTables::of($massPro)
            ->addColumn('project', function ($item) {
                return $item->project_nama ?: '-';
            })
            ->addColumn('quantity', function ($item) {
                return $item->quantity !== null ? $item->quantity : '-';
            })
            ->addColumn('waktu', function ($item) {
                return $item->waktu ?: '-';
            })
            ->addColumn('status', function ($item) {
                return $item->status ?: '-';
            })
            ->addColumn('action', function ($item) {
                if ($item->status === 'selesai') {
                    return '<span class="badge badge-success">Selesai</span>';
                }

                return '
                    <button class="btn btn-sm btn-success approveBtn" data-id="' . $item->id . '">Approve</button>
                    <button class="btn btn-sm btn-danger revisiBtn" data-id="' . $item->id . '">Revisi</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function updateStatus(Request $request, string $id)
    {
        $massPro = Mass_Production::findOrFail($id);
        $massPro->status = $request->status;
        $massPro->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function revisi(Request $request, string $id)
    {
        $massPro = Mass_Production::findOrFail($id);
        $massPro->status = 'revisi';
        $massPro->save();

        return response()->json(['message' => 'Status updated to revisi successfully']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
