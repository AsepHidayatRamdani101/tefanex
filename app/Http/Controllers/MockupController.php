<?php

namespace App\Http\Controllers;

use App\Models\Mockup;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MockupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedProjectId = $request->query('project_id');
        $designBriefStatus = null;

        if ($selectedProjectId) {
            $designBriefStatus = DB::table('design_briefs')
                ->where('project_id', $selectedProjectId)
                ->value('approval_status');
        }

        return view('mockup.index', compact('selectedProjectId', 'designBriefStatus'));
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

        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }
        
        // First, check if there's a project_id filter in the request
        $projectId = $request->get('project_id');
        
        // Get design brief records with approved status, then left join with mockup
        $query = DB::table('design_briefs')
            ->leftJoin('mockups', 'design_briefs.project_id', '=', 'mockups.project_id')
            ->join('timelines', 'design_briefs.project_id', '=', 'timelines.project_id')
            ->join('projects', 'timelines.project_id', '=', 'projects.id')
            ->select(
                DB::raw('COALESCE(mockups.id, 0) as id'),
                'design_briefs.project_id',
                'mockups.file_path',
                'mockups.status',
                'mockups.revision_note',
                'timelines.start_date',
                'timelines.end_date',
                'design_briefs.description as design_description',
                'design_briefs.approval_status',
                'design_briefs.keterangan as design_brief_revisi',
                'projects.judul',
                'design_briefs.reference_files',
                'design_briefs.reference_file',
                DB::raw('COALESCE(mockups.created_at, NOW()) as created_at')
            )
            ->where('design_briefs.approval_status', 'approved');
        
        // If there's a project_id filter, apply it
        if ($projectId) {
            $query->where('design_briefs.project_id', $projectId);
        }
        
        $projects = $query->get();

        return DataTables::of($projects)
            ->addIndexColumn()
            ->addColumn('judul', function ($project) {
                return $project->judul;
            })
            ->addColumn('deskripsi', function ($project) {
                return $project->design_description;
            })
            ->addColumn('file', function ($project) {
                $files = [];
                
                // Check if there are multiple reference files (stored as JSON array)
                if (!empty($project->reference_files)) {
                    $referenceFiles = is_string($project->reference_files) 
                        ? json_decode($project->reference_files, true) 
                        : $project->reference_files;
                    
                    if (is_array($referenceFiles)) {
                        $files = $referenceFiles;
                    }
                }
                
                // Fallback to single reference file if no array
                if (empty($files) && !empty($project->reference_file)) {
                    $files = [$project->reference_file];
                }
                
                // Generate HTML for all files
                if (empty($files)) {
                    return '<span class="badge badge-secondary">Tidak ada file</span>';
                }
                
                $html = '<div class="file-list">';
                foreach ($files as $file) {
                    if (!empty($file)) {
                        $filename = basename($file);
                        $html .= '<a href="' . $file . '" class="btn btn-sm btn-primary mb-1" target="_blank">'
                                . '<i class="fas fa-download"></i> ' . substr($filename, 0, 20) . ''
                                . (strlen($filename) > 20 ? '...' : '') . '</a><br/>';
                    }
                }
                $html .= '</div>';
                
                return $html;
            })
            ->addColumn('revisi', function ($project) {
                return $project->revision_note ?: ($project->design_brief_revisi ?? '');
            })
            ->addColumn('status', function ($project) {
                return strtolower((string) ($project->approval_status ?? 'pending'));
            })
             ->addColumn('hasil', function ($project) {
                return $project->file_path ?? '';
            })
           
           
            ->addColumn('action', function ($project) use ($user) {
                $approvalStatus = strtolower((string) ($project->approval_status ?? ''));

                if ($approvalStatus !== 'approved') {
                    return '<button class="btn btn-sm btn-secondary" disabled title="Design Brief belum disetujui">Upload</button>';
                }

                // If mockup doesn't exist yet (id = 0 from COALESCE), show Upload button with project_id
                // If mockup exists, show Upload, Approve, and View buttons with mockup id
                if ($project->id == 0) {
                    return '<button class="btn btn-sm btn-warning addBtn" data-id="' . $project->project_id . '" title="Upload mockup baru">Upload</button>';
                }
                return '
                    <button class="btn btn-sm btn-warning addBtn" data-id="' . $project->id . '">Upload</button>        
                    <button class="btn btn-sm btn-success approveBtn" data-id="' . $project->id . '">Approve</button>
                    <button class="btn btn-sm btn-info lihatBtn" data-id="' . $project->id . '">Lihat</button>
                    ';          
            })
            ->rawColumns(['file', 'hasil', 'action'])
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
        //ambil data mockup berdasarkan id
        $mockup = Mockup::findOrFail($id);
        return response()->json($mockup);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $mockup = Mockup::findOrFail($id);
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/reference_files', $filename);
        $mockup->file_path = 'storage/reference_files/' . $filename;
        $mockup->save();

        // update status di project
        $project = Project::findOrFail($mockup->project_id);
        $project->status = "design";
        $project->save();

        return response()->json(['status' => 'berhasil']);
    }

     public function updateStatus(Request $request, string $id)
    {
        $mockup = Mockup::findOrFail($id);
        $mockup->status = $request->status;
        $mockup->revision_note = $request->keterangan ?? '-';
        $mockup->note = $request->note;
         
        $mockup->save();

        return response()->json(['status' => 'berhasil']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
