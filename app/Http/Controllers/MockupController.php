<?php

namespace App\Http\Controllers;

use App\Models\Mockup;
use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MockupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('mockup.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Data for datatables
     */
    public function data(Request $request)
    {
       $projects = Mockup::join('design_briefs', 'mockups.project_id', '=', 'design_briefs.project_id')
            ->join('timelines', 'mockups.project_id', '=', 'timelines.project_id')
            ->join('projects', 'timelines.project_id', '=', 'projects.id')
            ->select('mockups.*','timelines.start_date', 'timelines.end_date', 
            'design_briefs.description as design_description', 'design_briefs.approval_status', 
            'design_briefs.approved_by', 'design_briefs.description as deskripsi', 
            'projects.judul as judul','design_briefs.reference_file as file')
            ->where('design_briefs.approval_status', 'approved')
            ->get();

        return DataTables::of($projects)
            ->addIndexColumn()
            ->addColumn('judul', function ($project) {
                return $project->judul;
            })
            ->addColumn('deskripsi', function ($project) {
                return $project->design_description;
            })
            ->addColumn('file', function ($project) {
                return $project->file ?? '';
            })
            ->addColumn('waktu', function ($project) {
                return date('d-m-Y', strtotime($project->start_date)) . ' s.d ' . date('d-m-Y', strtotime($project->end_date));
            })
           
            ->addColumn('action', function ($project) {
                return '
                <button class="btn btn-sm btn-warning addBtn" data-id="' . $project->id . '">Upload</button>
                ';
            })
            ->make(true);
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
