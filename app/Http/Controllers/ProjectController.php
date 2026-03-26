<?php

namespace App\Http\Controllers;

use App\Models\Design_Brief;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gurus = User::role('guru')->get();
        $users = User::all();
        $projects = Project::all();
        return view('projects.index', compact('gurus', 'users', 'projects'));
    }

    public function data()
    {
        $projects = Project::with('guru')->select('projects.*');

        return DataTables::of($projects)
            ->addColumn('guru', function ($project) {
                return $project->guru->name;
            })
            ->addColumn('action', function ($project) {
                return '
                <button class="btn btn-sm btn-warning editBtn" data-id="' . $project->id . '">Edit</button>
                <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $project->id . '">Delete</button>
                <button class="btn btn-sm btn-primary lihatAnggota" data-id="' . $project->id . '">Lihat Anggota</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
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
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'guru_id' => 'required|exists:users,id',
            'client' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $project = Project::create($request->all());

        // Laravel otomatis tahu project_id-nya adalah ID dari $project
        $project->designBrief()->create([
            'project_id' => $project->id,
            'description' => '',
            'approved_by' => null,
            'approval_status' => 'pending',
        ]);

        // Buat timeline
        $project->timeline()->create([
            'project_id' => $project->id,
            'start_date' => now(),
            'end_date' => now(),
            'created_by' => auth()->user()->id,
        ]);

        //buat mockup
        $project->mockups()->create([
            'project_id' => $project->id,
            'status' => 'pending',
        ]);
    

        return response()->json(['message' => 'Project berhasil dibuat', 'project' => $project]);



    }

  

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::join('design_briefs', 'projects.id', '=', 'design_briefs.project_id')->where('projects.id', $id)->select('projects.*', 'design_briefs.description as design_description', 'design_briefs.approval_status', 'design_briefs.approved_by','design_briefs.id as design_brief_id')->first();
        return response()->json($project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'guru_id' => 'required|exists:users,id',
            'client' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        // Update data project
        $project = Project::findOrFail($id);
        $project->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'guru_id' => $request->guru_id,
            'client' => $request->client,
            'status' => $request->status,
        ]);



        return response()->json(['message' => 'Project berhasil diupdate', 'project' => $project]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project berhasil dihapus']);
    }
}
