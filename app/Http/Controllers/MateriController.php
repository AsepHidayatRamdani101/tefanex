<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MateriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderBy('judul')->get();
        return view('materi.index', compact('projects'));
    }

    public function data()
    {
        $materials = Material::with(['creator', 'project'])->select('materials.*');

        return DataTables::of($materials)
            ->addColumn('creator', function (Material $material) {
                return $material->creator?->name ?? '-';
            })
            ->addColumn('project', function (Material $material) {
                return $material->project?->judul ?? '-';
            })
            ->editColumn('type', function (Material $material) {
                $types = explode(',', $material->type);
                $badgeColors = [
                    'video' => 'info',
                    'pdf' => 'danger',
                    'text' => 'success',
                ];
                $badges = array_map(function($type) use ($badgeColors) {
                    $color = $badgeColors[trim($type)] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucfirst(trim($type)) . '</span>';
                }, $types);
                return implode(' ', $badges);
            })
            ->editColumn('created_at', function (Material $material) {
                return $material->created_at?->format('Y-m-d');
            })
            ->addColumn('action', function (Material $material) {
                return '<button class="btn btn-sm btn-primary editBtn" data-id="' . $material->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deleteBtn" data-id="' . $material->id . '">Hapus</button>';
            })
            ->rawColumns(['type', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf|max:102400',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        // Validate that at least one content type is provided
        if (!$request->filled('content') && !$request->filled('video_link') && !$request->hasFile('file')) {
            return response()->json([
                'message' => 'Minimal harus ada satu dari: teks, video, atau PDF'
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
        }

        // Determine available content types
        $types = [];
        if ($request->filled('content')) {
            $types[] = 'text';
        }
        if ($request->filled('video_link')) {
            $types[] = 'video';
        }
        if ($filePath) {
            $types[] = 'pdf';
        }

        $validated['created_by'] = Auth::id();
        $validated['file_path'] = $filePath ? 'storage/' . $filePath : null;
        $validated['type'] = implode(',', $types); // Store as comma-separated types

        Material::create($validated);

        return response()->json(['message' => 'Materi berhasil dibuat']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Material::with('creator')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Material::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf|max:102400',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        // Validate that at least one content type is provided
        $hasExistingContent = !empty($material->content);
        $hasExistingVideo = !empty($material->video_link);
        $hasExistingFile = !empty($material->file_path);

        if (!$request->filled('content') && !$request->filled('video_link') && !$request->hasFile('file') 
            && !$hasExistingContent && !$hasExistingVideo && !$hasExistingFile) {
            return response()->json([
                'message' => 'Minimal harus ada satu dari: teks, video, atau PDF'
            ], 422);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
            $validated['file_path'] = 'storage/' . $filePath;
        }

        // Determine available content types
        $types = [];
        if ($request->filled('content')) {
            $types[] = 'text';
        } elseif ($hasExistingContent) {
            $types[] = 'text';
        }

        if ($request->filled('video_link')) {
            $types[] = 'video';
        } elseif ($hasExistingVideo) {
            $types[] = 'video';
        }

        if (!empty($validated['file_path'])) {
            $types[] = 'pdf';
        } elseif ($hasExistingFile) {
            $types[] = 'pdf';
        }

        $validated['type'] = implode(',', $types); // Store as comma-separated types

        $material->update($validated);

        return response()->json(['message' => 'Materi berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return response()->json(['message' => 'Materi berhasil dihapus']);
    }
}
