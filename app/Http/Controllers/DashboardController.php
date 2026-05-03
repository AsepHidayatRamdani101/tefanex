<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Project_Member;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($user->hasRole('super_admin')) {
            return view('dashboard.admin');
        } else if ($user->hasRole('guru')) {
            return $this->guruDashboard($user);
        } else if ($user->hasRole('siswa')) {
            return view('dashboard.siswa');
        } else if ($user->hasRole('kepala_tefa')) {
            return $this->kepalaDashboard($user);
        } else if ($user->hasRole('bendahara')) {
            return view('dashboard.bendahara');
        } else if ($user->hasRole('marketing')) {
            return view('dashboard.marketing');
        } else if ($user->hasRole(roles: 'designer')) {
            return view('dashboard.designer');
        } else if ($user->hasRole('produksi')) {
            return view('dashboard.produksi');
        } else {
            abort(403, 'Unauthorized');
        }
    }

    private function guruDashboard($user)
    {
        // Total projects created by guru
        $totalProjects = Project::where('guru_id', $user->id)->count();
        
        // Projects with progress (not in 'awal' status)
        $activeProjects = Project::where('guru_id', $user->id)
            ->where('status', '<>', 'awal')
            ->count();
        
        // Get all projects for display
        $projects = Project::where('guru_id', $user->id)
            ->with('project_members')
            ->with('designBrief')
            ->latest()
            ->get();
        
        // Calculate project progress percentages
        $projectsWithProgress = $projects->map(function($project) {
            $progress = $this->calculateProjectProgress($project);
            return (object)[
                'id' => $project->id,
                'judul' => $project->judul,
                'client' => $project->client,
                'status' => $project->status,
                'members_count' => $project->project_members->count(),
                'progress' => $progress,
            ];
        });
        
        // Student activity count
        $studentActivityCount = Project_Member::whereHas('project', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->count();

        return view('dashboard.guru', [
            'totalProjects' => $totalProjects,
            'activeProjects' => $activeProjects,
            'studentActivityCount' => $studentActivityCount,
            'projects' => $projectsWithProgress,
        ]);
    }

    private function calculateProjectProgress($project)
    {
        $stages = [
            'awal' => 0,
            'design_brief' => 20,
            'mockup' => 40,
            'produksi' => 60,
            'qc' => 80,
            'selesai' => 100,
        ];
        
        return $stages[$project->status] ?? 0;
    }

    private function kepalaDashboard($user)
    {
        // Aggregated totals for kepala TEFA (global view)
        $totalProjects = Project::count();

        $activeProjects = Project::where('status', '<>', 'awal')->count();

        $projects = Project::with('project_members')
            ->with('designBrief')
            ->latest()
            ->get();

        $projectsWithProgress = $projects->map(function($project) {
            $progress = $this->calculateProjectProgress($project);
            return (object)[
                'id' => $project->id,
                'judul' => $project->judul,
                'client' => $project->client,
                'status' => $project->status,
                'members_count' => $project->project_members->count(),
                'progress' => $progress,
            ];
        });

        $studentActivityCount = Project_Member::count();

        return view('dashboard.kepala_tefa', [
            'totalProjects' => $totalProjects,
            'activeProjects' => $activeProjects,
            'studentActivityCount' => $studentActivityCount,
            'projects' => $projectsWithProgress,
        ]);
    }
}
