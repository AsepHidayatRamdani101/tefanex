<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Project_Member;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        $users = User::all();
        $kelas = Kelas::all();

        return view('project_members.index', compact('projects', 'users', 'kelas'));
    }

    /**
     * Get students by class
     */
    public function getStudentsByClass(Request $request)
    {
        try {
            if (!$request->has('kelas_id') || !$request->kelas_id) {
                return response()->json([]);
            }

            $students = Siswa::where('kelas_id', $request->kelas_id)
                ->whereNotNull('user_id')
                ->with('user')
                ->get()
                ->map(function ($siswa) {
                    // Make sure user exists before accessing
                    if ($siswa->user) {
                        return [
                            'id' => $siswa->user_id,
                            'name' => $siswa->nama . ' (' . $siswa->nim . ')',
                        ];
                    }
                })
                ->filter() // Remove null values
                ->values() // Reindex array
                ->toArray();

            return response()->json($students, 200);
        } catch (\Exception $e) {
            \Log::error('Get Students by Class Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat siswa'], 500);
        }
    }

    public function data(Request $request)
    {
        $query = Project_Member::with('user', 'project');
        
        // Filter by project if provided
        if ($request->has('project') && $request->project != '') {
            $query->where('project_id', $request->project);
        }
        
        $projectMembers = $query->get();

        return datatables()->of($projectMembers)
            ->addColumn('deskripsi', function ($projectMember) {
                return $projectMember->project->deskripsi ?? '-';
            })
            ->addColumn('anggota', function ($projectMember) {
                return $projectMember->user->name;
            })
            ->addColumn('project', function ($projectMember) {
                return $projectMember->project->judul;
            })
            ->addColumn('tugas', function ($projectMember) {
                return $projectMember->role_in_project;
            })
            ->addColumn('action', function ($projectMember) {
                return '
                <button class="btn btn-sm btn-warning editBtnMember" data-id="' . $projectMember->id . '">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger deleteBtnMember" data-id="' . $projectMember->id . '">
                    <i class="fas fa-trash"></i> Delete
                </button>
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
        //validasi data
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'anggota_id' => 'required|exists:users,id',
            'tugas' => 'required|string|max:255',
        ]);

        $projectMember = Project_Member::create([
            'project_id' => $request->project_id,
            'user_id' => $request->anggota_id,
            'role_in_project' => $request->tugas,
        ]);

        return response()->json(['success' => 'Project Member added successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $projectMember = Project_Member::with('user', 'project')->findOrFail($id);
        return response()->json($projectMember);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $projectMember = Project_Member::with(['user.siswa', 'project'])->findOrFail($id);

        return response()->json([
            'id' => $projectMember->id,
            'project_id' => $projectMember->project_id,
            'user_id' => $projectMember->user_id,
            'kelas_id' => $projectMember->user->siswa ? $projectMember->user->siswa->kelas_id : null,
            'role_in_project' => $projectMember->role_in_project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //validasi data
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'anggota_id' => 'required|exists:users,id',
            'tugas' => 'required|string|max:255',
        ]);

        $projectMember = Project_Member::findOrFail($id);
        $projectMember->update([
            'project_id' => $request->project_id,
            'user_id' => $request->anggota_id,
            'role_in_project' => $request->tugas,
        ]);

        return redirect()->route('project-members.index')->with('success', 'Project Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $projectMember = Project_Member::findOrFail($id);
        $projectMember->delete();

        return response()->json(['success' => 'Project Member deleted successfully.']);
    }
}
