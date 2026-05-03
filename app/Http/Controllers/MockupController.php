<?php

namespace App\Http\Controllers;

use App\Models\Mockup;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }
       $projects = Mockup::join('design_briefs', 'mockups.project_id', '=', 'design_briefs.project_id')
            ->join('timelines', 'mockups.project_id', '=', 'timelines.project_id')
            ->join('projects', 'timelines.project_id', '=', 'projects.id')
            ->select('mockups.*','timelines.start_date', 'timelines.end_date', 
            'design_briefs.description as design_description', 'design_briefs.approval_status', 
            'design_briefs.approved_by', 'design_briefs.description as deskripsi', 
            'projects.judul as judul','design_briefs.reference_files', 'design_briefs.reference_file')
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
                return $project->revision_note ?? '';
            })
             ->addColumn('hasil', function ($project) {
                return $project->file_path ?? '';
            })
           
           
            ->addColumn('action', function ($project) use ($user) {
               
                    return '
                    <button class="btn btn-sm btn-warning addBtn" data-id="' . $project->id . '">Upload</button>        
                    
                    <button class="btn btn-sm btn-success approveBtn" data-id="' . $project->id . '">approve</button>
                    <button class="btn btn-sm btn-info lihatBtn" data-id="' . $project->id . '">lihat</button>
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
