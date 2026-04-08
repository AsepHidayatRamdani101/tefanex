<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Material;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = Material::orderBy('title')->get();
        return view('test.index', compact('materials'));
    }

    public function data()
    {
        $tests = Test::with(['material'])->select('tests.*');

        return DataTables::of($tests)
            ->addColumn('material_title', function (Test $test) {
                return $test->material?->title ?? '-';
            })
            ->editColumn('type', function (Test $test) {
                $badge = match($test->type) {
                    'pretest' => 'primary',
                    'posttest' => 'success',
                    default => 'secondary'
                };
                return '<span class="badge badge-' . $badge . '">' . ucfirst($test->type) . '</span>';
            })
            ->editColumn('created_at', function (Test $test) {
                return $test->created_at?->format('Y-m-d');
            })
            ->addColumn('action', function (Test $test) {
                return '<button class="btn btn-sm btn-primary editBtn" data-id="' . $test->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deleteBtn" data-id="' . $test->id . '">Hapus</button>';
            })
            ->rawColumns(['type', 'action'])
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
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'type' => 'required|in:pretest,posttest',
        ]);

        Test::create($validated);

        return response()->json(['message' => 'Test berhasil dibuat']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Test::with('material')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Test::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $test = Test::findOrFail($id);

        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'type' => 'required|in:pretest,posttest',
        ]);

        $test->update($validated);

        return response()->json(['message' => 'Test berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $test = Test::findOrFail($id);
        $test->delete();

        return response()->json(['message' => 'Test berhasil dihapus']);
    }
}
