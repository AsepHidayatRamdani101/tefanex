<?php

namespace App\Http\Controllers;

use App\Models\Design_Brief;
use App\Models\Project;
use App\Models\Project_Member;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DesignBriefController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //view design brief
        return view('design_brief.index');
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
        $projectMember = Project_Member::where('user_id', auth()->id())
        ->with(['user','project','design_briefs'])
        ->select('project_members.*');
        return DataTables::of($projectMember )
            ->addColumn('project_name', function ($project) {
                return $project->project ? $project->project->judul : '-';
            })
            ->addColumn('deskripsi', function ($project) {
                return $project->project ? $project->project->deskripsi : '-';
            })
            ->addColumn('klien', function ($project) {
                return $project->project ? $project->project->client : '-';
            })
            ->addColumn('user_name', function ($project) {
                return $project->user ? $project->user->name : '-';
            })
            ->addColumn('action', function ($project) {
                
                return '
                    <button class="btn btn-sm btn-primary tambahBtn" data-id="' . $project->id . '">Tambah</button>
                    <button class="btn btn-sm btn-secondary lihatBtn" data-id="' . $project->id . '">Lihat</button>
                    <button class="btn btn-sm btn-warning editBtn" data-id="' . $project->id . '">Edit</button>
                ';
            })
            ->addIndexColumn('id')
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
        //tampil detail design brief
        $designBrief = Design_Brief::with(['project', 'user'])->findOrFail($id);
        return response()->json($designBrief);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //tampil data design brief untuk edit
        $designBrief = Design_Brief::with(['project', 'user'])->findOrFail($id);
        return response()->json($designBrief);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //validasi data
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'required|string',
            'target_market' => 'required|string',
            'budget' => 'required|numeric',
            'reference_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $designBrief = Design_Brief::findOrFail($id);
        $designBrief->project_id = $request->project_id;
        $designBrief->description = $request->description;
        $designBrief->target_market = $request->target_market;
        $designBrief->budget = $request->budget;
        if ($request->hasFile('reference_file')) {
            $file = $request->file('reference_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/reference_files', $filename);
            $designBrief->reference_file = 'storage/reference_files/' . $filename;
        }
        $designBrief->save();

        //update status di project
        $project = Project::findOrFail($designBrief->project_id);
        $project->status = "design_brief";
        $project->save();

        return response()->json(['message' => 'Design Brief updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
