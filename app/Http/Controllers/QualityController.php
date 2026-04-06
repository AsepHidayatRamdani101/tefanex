<?php

namespace App\Http\Controllers;

use App\Models\Mockup;
use App\Models\Project;
use App\Models\Quality_Control;
use App\Models\Timeline;
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
        $produksi = Project::leftJoin('quality_controls', 'projects.id', '=', 'quality_controls.project_id')
            ->join('design_briefs', 'projects.id', '=', 'design_briefs.project_id')
            ->join('mockups', 'projects.id', '=', 'mockups.project_id')
            ->join('timelines', 'projects.id', '=', 'timelines.project_id')
            ->where('design_briefs.approval_status', 'approved')
            ->select('projects.judul as project_nama', 
            'projects.id as pr',
            'design_briefs.description as deskripsi', 
            'timelines.end_date as waktu',
            'quality_controls.status as status',
            'quality_controls.note as revisi',
            'quality_controls.checklist_result as checklist',
            'mockups.file_path as file','quality_controls.*')->get();


        return DataTables::of($produksi)
            ->addColumn('project', function ($produksi) {
                return $produksi->project_nama ? $produksi->project_nama : '-';
            })
            ->addColumn('deskripsi', function ($produksi) {
                return $produksi->deskripsi ? $produksi->deskripsi : '-';
            })
            ->addColumn('file', function ($produksi) {
                return $produksi->file ? $produksi->file : '-';
            })
            ->addColumn('waktu', function ($produksi) {
                return $produksi->waktu ? $produksi->waktu : '-';
            })
            ->addColumn('status', function ($produksi) {
                return $produksi->status ? $produksi->status : '-';
            })
            ->addColumn('revisi', function ($produksi) {
                return $produksi->revisi ? $produksi->revisi : '-';
            })
            ->addColumn('action', function ($produksi) {
                return '
                        <button class="btn btn-sm btn-primary checklistBtn" data-id="' . $produksi->pr . '">Checklist</button>
                        <button class="btn btn-sm btn-warning editBtn" data-id="' . $produksi->pr . '">Edit</button>
                        <button class="btn btn-sm btn-info lihatBtn" data-id="' . $produksi->pr . '">Lihat</button>
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
        //tampilkan detail quality control berdasarkan ID with project produksi mockup timeline
        $quality_control = Quality_Control::join('projects', 'quality_controls.project_id', '=', 'projects.id')
            ->join('design_briefs', 'projects.id', '=', 'design_briefs.project_id')
            ->join('mockups', 'projects.id', '=', 'mockups.project_id')
            ->join('productions', 'projects.id', '=', 'productions.project_id')
            ->where('projects.id', $id)
            ->select('quality_controls.*', 
            'projects.judul as project_nama', 
           'productions.file_path as produksi_file',
            'mockups.file_path as mockup_file')->first();

        return response()->json($quality_control);
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
        // Validasi input
        $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
            'checklist_result' => 'nullable|string',
        ]);

        // Temukan quality control berdasarkan ID
        $quality_control = Quality_Control::findOrFail($id);

        // Perbarui data quality control
        $quality_control->status = $request->input('status');
        $quality_control->note = $request->input('note');
        $quality_control->checklist_result = $request->input('checklist_result');
        $quality_control->save();

        return response()->json(['message' => 'Quality control updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
