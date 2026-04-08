<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Material;
use App\Models\Test_Result;
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

    public function gradeIndex()
    {
        return view('grades.index');
    }

    public function gradeData()
    {
        $results = Test_Result::with(['test.material.project', 'user'])
            ->join('tests', 'test_results.test_id', '=', 'tests.id')
            ->join('materials', 'tests.material_id', '=', 'materials.id')
            ->join('project_members', 'project_members.user_id', '=', 'test_results.user_id')
            ->whereColumn('materials.project_id', 'project_members.project_id')
            ->select('test_results.*');

        return DataTables::of($results)
            ->addColumn('student_name', function (Test_Result $result) {
                return $result->user?->name ?? '-';
            })
            ->addColumn('project_name', function (Test_Result $result) {
                return $result->test->material->project?->name ?? '-';
            })
            ->addColumn('material_title', function (Test_Result $result) {
                return $result->test->material?->title ?? '-';
            })
            ->addColumn('test_type', function (Test_Result $result) {
                $badge = match($result->test->type) {
                    'pretest' => 'primary',
                    'posttest' => 'success',
                    default => 'secondary'
                };
                return '<span class="badge badge-' . $badge . '">' . ucfirst($result->test->type) . '</span>';
            })
            ->addColumn('score', function (Test_Result $result) {
                return $result->manual_score !== null ? $result->manual_score . '%' : $result->score . '%';
            })
            ->addColumn('task_score', function (Test_Result $result) {
                return $result->task_score !== null ? $result->task_score . '%' : '-';
            })
            ->addColumn('attitude_note', function (Test_Result $result) {
                return $result->attitude_note ? substr($result->attitude_note, 0, 50) . (strlen($result->attitude_note) > 50 ? '...' : '') : '-';
            })
            ->addColumn('action', function (Test_Result $result) {
                $studentName = htmlspecialchars($result->user?->name ?? '-', ENT_QUOTES, 'UTF-8');
                $materialTitle = htmlspecialchars($result->test->material?->title ?? '-', ENT_QUOTES, 'UTF-8');
                $attitudeNote = htmlspecialchars($result->attitude_note ?? '', ENT_QUOTES, 'UTF-8');
                return '<button class="btn btn-sm btn-primary editGradeBtn" data-id="' . $result->id . '" data-student="' . $studentName . '" data-material="' . $materialTitle . '" data-manual-score="' . ($result->manual_score ?? '') . '" data-task-score="' . ($result->task_score ?? '') . '" data-attitude-note="' . $attitudeNote . '">Nilai</button>';
            })
            ->rawColumns(['test_type', 'action'])
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
