<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Project;
use App\Models\Material;
use App\Models\Project_Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMaterialController extends Controller
{
    /**
     * Display materials for authenticated student based on project membership
     */
    public function materials(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('dashboard')
                ->with('error', 'Data siswa tidak ditemukan. Hubungi administrator.');
        }

        // Get projects where student is a member
        $projectMembers = Project_Member::where('user_id', $user->id)
            ->with('project')
            ->get();

        $projectIds = $projectMembers->pluck('project_id');

        $materialsQuery = Material::whereIn('project_id', $projectIds)
            ->with('project', 'creator')
            ->orderBy('created_at', 'desc');

        if ($request->filled('project_id') && $projectIds->contains($request->project_id)) {
            $materialsQuery->where('project_id', $request->project_id);
        }

        $materials = $materialsQuery->paginate(12)->appends($request->only('project_id'));

        return view('student.materials.index', compact('materials', 'siswa', 'projectMembers'));
    }

    /**
     * Display tasks/assignments for authenticated student based on project membership and role
     */
    public function tasks(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('dashboard')
                ->with('error', 'Data siswa tidak ditemukan. Hubungi administrator.');
        }

        // Get projects where student is a member with their role
        $projectMembers = Project_Member::where('user_id', $user->id)
            ->with(['project' => function ($q) {
                $q->with(['designBrief', 'mockups', 'productions', 'qualityControls']);
            }])
            ->get();

        // Collect tasks based on role
        $tasks = collect();
        
        foreach ($projectMembers as $member) {
            $project = $member->project;
            $role = strtolower($member->role_in_project);

            // Marketing: Design Brief
            if (in_array($role, ['marketing', 'marketing (pemasaran)'])) {
                $designBrief = $project->designBrief;
                if ($designBrief) {
                    $tasks->push([
                        'type' => 'Design Brief',
                        'role' => $member->role_in_project,
                        'project_id' => $project->id,
                        'project_name' => $project->judul,
                        'title' => 'Design Brief',
                        'task_description' => 'Lengkapi Design Brief untuk project ini dengan detail target pasar, budget, dan referensi visual.',
                        'description' => $designBrief->deskripsi ?? '-',
                        'status' => $designBrief->status ?? $project->status,
                        'created_at' => $designBrief->created_at,
                        'data' => $designBrief
                    ]);
                }
            }

            // Designer: Mockup
            if (in_array($role, ['designer', 'desain'])) {
                $mockups = $project->mockups ?? collect();
                foreach ($mockups as $mockup) {
                    $tasks->push([
                        'type' => 'Mockup',
                        'role' => $member->role_in_project,
                        'project_id' => $project->id,
                        'project_name' => $project->judul,
                        'title' => 'Desain Mockup',
                        'task_description' => 'Upload hasil mockup desain sesuai dengan brief project dan pastikan file dapat ditinjau oleh tim.',
                        'description' => $mockup->deskripsi ?? '-',
                        'status' => $mockup->status ?? $project->status,
                        'created_at' => $mockup->created_at,
                        'data' => $mockup
                    ]);
                }
            }

            // Operator Produksi: Production
            if (in_array($role, ['operator produksi', 'operator_produksi', 'produksi'])) {
                $productions = $project->productions ?? collect();
                foreach ($productions as $production) {
                    $tasks->push([
                        'type' => 'Produksi',
                        'role' => $member->role_in_project,
                        'project_id' => $project->id,
                        'project_name' => $project->judul,
                        'title' => 'Proses Produksi',
                        'task_description' => 'Upload hasil produksi dan laporkan progress sesuai spesifikasi project.',
                        'description' => $production->deskripsi ?? '-',
                        'status' => $production->status ?? $project->status,
                        'created_at' => $production->created_at,
                        'data' => $production
                    ]);
                }
            }

            // QC: Quality Control
            if (in_array($role, ['qc', 'quality control', 'quality_control', 'kontrol kualitas'])) {
                $qualityControls = $project->qualityControls ?? collect();
                foreach ($qualityControls as $qc) {
                    $tasks->push([
                        'type' => 'Quality Control',
                        'role' => $member->role_in_project,
                        'project_id' => $project->id,
                        'project_name' => $project->judul,
                        'title' => 'Pemeriksaan Kualitas',
                        'task_description' => 'Lakukan pemeriksaan kualitas pada hasil desain atau produksi, dan catat semua revisi yang diperlukan.',
                        'description' => $qc->deskripsi ?? '-',
                        'status' => $qc->status ?? $project->status,
                        'created_at' => $qc->created_at,
                        'data' => $qc
                    ]);
                }
            }
        }

        // Apply project filter when requested
        if ($request->filled('project_id') && $projectMembers->pluck('project_id')->contains($request->project_id)) {
            $tasks = $tasks->filter(function ($task) use ($request) {
                return $task['project_id'] == $request->project_id;
            })->values();
        }

        // Sort by created_at descending
        $tasks = $tasks->sortByDesc('created_at')->values();

        $selectedProjectId = $request->input('project_id');

        return view('student.materials.tasks', compact('tasks', 'siswa', 'projectMembers', 'selectedProjectId'));
    }

    /**
     * Display detail of a specific material
     */
    public function showMaterial(Material $material)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        // Check if student has access to this material's project
        $hasMember = Project_Member::where('user_id', $user->id)
            ->where('project_id', $material->project_id)
            ->exists();

        if (!$hasMember) {
            abort(403, 'Anda tidak memiliki akses ke materi ini');
        }

        $material->load('project', 'creator');

        return view('student.materials.detail', compact('material', 'siswa'));
    }
}
