<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Quality_Control;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class QualityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('quality.index');
    }

    public function data()
    {
        $produksi = Quality_Control::leftJoin('productions', 'projects.id', '=', 'productions.project_id')
            ->join('design_briefs', 'projects.id', '=', 'design_briefs.project_id')
            ->join('mockups', 'projects.id', '=', 'mockups.project_id')
            ->join('timelines', 'projects.id', '=', 'timelines.project_id')
            ->join('projects', 'quality_controls.project_id', '=', 'projects.id')
            ->where('design_briefs.approval_status', 'approved')
            ->select('projects.judul as project_nama', 
            'design_briefs.description as deskripsi', 
            'mockups.file_path as file','quality_controls.*')->get();


        return DataTables::of($produksi)
            ->addColumn('project', function ($produksi) {
                return $produksi->project_nama ? $produksi->project_nama : '-';
            })
            ->addColumn('deskripsi', function ($produksi) {
                return $produksi->deskripsi ? $produksi->deskripsi : '-';
            })
            ->addColumn('file', function ($produksi) {
                return $produksi->mockup_file ? $produksi->mockup_file : '-';
            })
            ->addColumn('action', function ($produksi) {
                return '
                        <button class="btn btn-sm btn-info lihatBtn" data-id="' . $produksi->id . '">Lihat</button>
                        ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function updateStatus(Request $request, string $id)
    {
        // Implementasi logika untuk memperbarui status quality control
    }

    public function revisi(Request $request, string $id)
    {
        // Implementasi logika untuk mengirim revisi quality control
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
