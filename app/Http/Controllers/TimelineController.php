<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TimelineController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //view timeline
        return view('timeline.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function data(Request $request)
    {
        //get project by id
       
       $project = Timeline::join('design_briefs', 'timelines.project_id', '=', 'design_briefs.project_id')
            ->join('projects', 'timelines.project_id', '=', 'projects.id')
            ->select('timelines.*', 'design_briefs.description as design_description', 'design_briefs.approval_status', 'design_briefs.approved_by', 'design_briefs.description as deskripsi', 'projects.judul as judul')
            ->where('design_briefs.approval_status', 'approved')
            ->get();
        return DataTables::of($project)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary set_timeline" data-id="' . $row->id . '">Set Timeline</button>';
            })
            ->rawColumns(['action'])
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
        //validasi
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        //update timeline
        $timeline = Timeline::findOrFail($id);
        $timeline->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        //update project
        $project = Project::where('id', $timeline->project_id)->first();
        $project->update([
            'status' => 'timeline',
        ]);

        return response()->json(['message' => 'Timeline berhasil diupdate', 'timeline' => $timeline]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
