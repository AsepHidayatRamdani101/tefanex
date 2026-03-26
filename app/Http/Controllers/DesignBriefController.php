<?php

namespace App\Http\Controllers;

use App\Models\Design_Brief;
use App\Models\Project;
use App\Models\Project_Member;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
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
        $projectMember = Design_Brief::with(['project', 'user','project.project_members'])
            ->whereHas('project.project_members', function($query) {
                $query->where('project_members.user_id', auth()->id());
            })->select('design_briefs.*');

        if (auth()->user()->hasRole('guru|super_admin|kepala_tefa')) {
             $projectMember = Design_Brief::with(['project', 'user'])->select('design_briefs.*');
           
        
        }

        return DataTables::of($projectMember)
            ->addColumn('project_name', function ($projectMember) {
                return $projectMember->project ? $projectMember->project->judul : '-';
            })
            ->addColumn('deskripsi', function ($projectMember) {
                return $projectMember->project ? $projectMember->project->deskripsi : '-';
            })
            ->addColumn('klien', function ($projectMember) {
                return $projectMember->project ? $projectMember->project->client : '-';
            })
            ->addColumn('user_name', function ($projectMember) {
                return $projectMember->user ? $projectMember->user->name : '-';
            })

            ->addColumn('status', function ($projectMember) {

                return $projectMember->approval_status;

            })

            ->addColumn('action', function ($projectMember) {
                if (auth()->user()->hasRole('guru')) {
                    $btn = '<button class="btn btn-sm btn-primary tambahBtn" data-id="' . $projectMember->project->id . '">Tambah</button>';
                    $btn .= ' <button class="btn btn-sm btn-secondary lihatBtn" data-id="' . $projectMember->project->id . '">Lihat</button>';
                    $btn .= ' <button class="btn btn-sm btn-warning editBtn" data-id="' . $projectMember->project->id . '">Edit</button>';
                    return $btn;
                } else if (auth()->user()->hasRole('siswa')) {
                    $btn = '<button class="btn btn-sm btn-primary tambahBtn" data-id="' . $projectMember->project->id . '">Tambah</button>';
                    $btn .= ' <button class="btn btn-sm btn-secondary lihatBtn" data-id="' . $projectMember->project->id . '">Lihat</button>';
                    $btn .= ' <button class="btn btn-sm btn-warning editBtn" data-id="' . $projectMember->project->id . '">Edit</button>';
                    return $btn;
                } else if (auth()->user()->hasRole('kepala_tefa')) {
                    $btn = '<button class="btn btn-sm btn-secondary lihatBtn" data-id="' . $projectMember->project->id . '">Lihat</button>';
                    $btn .= ' <button class="btn btn-sm btn-success approveBtn" data-id="' . $projectMember->id . '">Aprove</button>';
                    $btn .= ' <button class="btn btn-sm btn-danger rejectBtn" data-id="' . $projectMember->id . '">Reject</button>';
                    return $btn;
                }
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
        //validasi data
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'required|string',
            'target_market' => 'required|string',
            'budget' => 'required|numeric',
            'reference_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $designBrief = new Design_Brief();
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

        return response()->json(['message' => 'Design Brief created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //tampil detail design brief
        $designBrief = Design_Brief::with(['project'])->where('project_id', $id)->first();
        return response()->json($designBrief);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //tampil data design brief untuk edit
        $designBrief = Design_Brief::with(['project'])->where('project_id', $id)->first();
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
     * Update status project
     */
    public function updateStatus(Request $request, string $id)
    {
        $designBrief = Design_Brief::findOrFail($id);
        $designBrief->approval_status = $request->status;
        $designBrief->approved_by = auth()->user()->id;
        $designBrief->keterangan = $request->keterangan;
        $designBrief->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
