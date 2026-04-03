<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('produksi.index');
    }

    public function data()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $produksi = Project::leftJoin('productions', 'projects.id', '=', 'productions.project_id')
            ->join('design_briefs', 'projects.id', '=', 'design_briefs.project_id')
            ->join('mockups', 'projects.id', '=', 'mockups.project_id')
            ->join('timelines', 'projects.id', '=', 'timelines.project_id')
            ->where('design_briefs.approval_status', 'approved')
            ->select(
                'projects.judul as project_nama',
                'design_briefs.description as deskripsi',
                'mockups.file_path as mockup_file',
                'productions.id as id',
                'timelines.end_date as waktu',
                'productions.status as status',
                'productions.description as revision_note'
            )
            ->get();



        return DataTables::of($produksi)
            ->addColumn('project', function ($produksi) {
                return $produksi->project_nama ? $produksi->project_nama : '-';
            })

            ->addColumn('deskripsi', function ($produksi) {
                return $produksi->deskripsi ? $produksi->deskripsi : '-';
            })

            ->addColumn('file', function ($produksi) {
                return $produksi->mockup_file ? $produksi->mockup_file : '-';
            })

            ->addColumn('status', function ($produksi) {
                return $produksi->status ? $produksi->status : '-';
            })

            ->addColumn('revisi', function ($produksi) {
                return $produksi->revision_note ? $produksi->revision_note : '-';
            })

            ->addColumn('waktu', function ($produksi) {
                return $produksi->waktu ? $produksi->waktu : '-';
            })

            ->addColumn('action', function ($produksi) use ($user) {
                    return '
                    <button class="btn btn-sm btn-success approveBtn" data-id="' . $produksi->id . '">Approve</button>
                    <button class="btn btn-sm btn-danger revisiBtn" data-id="' . $produksi->id . '">Revisi</button>
                    <button class="btn btn-sm btn-primary tambahBtn" data-id="' . $produksi->id . '">Tambah</button>
                    <button class="btn btn-sm btn-info lihatBtn" data-id="' . $produksi->id . '">Lihat</button>
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produksi = Production::findOrFail($id);
        //return ajax response with produksi data
        return response()->json($produksi);
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
        //buat upload file 
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('produksi_files', $fileName, 'public');

        $production = Production::findOrFail($id);
        $production->file_path = 'storage/' . $filePath;
        $production->status = 'proses';
        $production->save();
        return response()->json(['message' => 'File uploaded successfully']);
    }


     public function updateStatus(Request $request, string $id)
    {
        $production = Production::findOrFail($id);
        $production->status = $request->status;
        $production->save();
        return response()->json(['message' => 'Status updated successfully']);
    }

    public function revisi(Request $request, string $id)
    {
        $production = Production::findOrFail($id);
        $production->status = 'revisi';
        $production->description = $request->revisi_note;
        $production->save();
        return response()->json(['message' => 'Revisi submitted successfully']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
