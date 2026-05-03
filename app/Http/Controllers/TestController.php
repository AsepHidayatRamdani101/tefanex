<?php

namespace App\Http\Controllers;

use App\Exports\GradeExport;
use App\Models\Kelas;
use App\Models\Test;
use App\Models\Material;
use App\Models\Project;
use App\Models\Test_Result;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
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
        $kelasList = Kelas::orderBy('name')->get();
        $projects = Project::orderBy('judul')->get();

        return view('grades.index', compact('kelasList', 'projects'));
    }

    public function gradeData()
    {
        $results = $this->buildGradeSummary(request());

        return DataTables::of($results)
            ->addColumn('student_name', function (Test_Result $result) {
                $studentName = htmlspecialchars($result->user?->name ?? '-', ENT_QUOTES, 'UTF-8');
                $taskName = trim((string) ($result->task_name ?? ''));

                if ($taskName === '') {
                    return $studentName;
                }

                $taskLabel = htmlspecialchars($taskName, ENT_QUOTES, 'UTF-8');

                return $studentName . ' <span class="badge badge-info ml-1">' . $taskLabel . '</span>';
            })
            ->addColumn('kelas_name', function (Test_Result $result) {
                return $result->user?->siswa?->kelas?->name ?? '-';
            })
            ->addColumn('project_name', function (Test_Result $result) {
                $project = htmlspecialchars($result->project_name ?? '-', ENT_QUOTES, 'UTF-8');
                $material = htmlspecialchars($result->material_title ?? '-', ENT_QUOTES, 'UTF-8');

                return $project . '<br><span class="text-muted small">Materi: ' . $material . '</span>';
            })
            ->addColumn('pretest_score', function (Test_Result $result) {
                return $this->formatGradeValue($result->pretest_score);
            })
            ->addColumn('posttest_score', function (Test_Result $result) {
                return $this->formatGradeValue($result->posttest_score);
            })
            ->addColumn('task_score', function (Test_Result $result) {
                return $this->formatGradeValue($result->task_score_value);
            })
            ->addColumn('average_score', function (Test_Result $result) {
                return $this->formatGradeValue($result->average_score);
            })
            ->addColumn('attitude_note', function (Test_Result $result) {
                return $result->attitude_note ? htmlspecialchars($result->attitude_note, ENT_QUOTES, 'UTF-8') : '-';
            })
            ->addColumn('action', function (Test_Result $result) {
                $studentName = htmlspecialchars($result->user?->name ?? '-', ENT_QUOTES, 'UTF-8');
                $projectName = htmlspecialchars($result->project_name ?? '-', ENT_QUOTES, 'UTF-8');
                $materialTitle = htmlspecialchars($result->material_title ?? '-', ENT_QUOTES, 'UTF-8');
                $taskScore = htmlspecialchars((string) ($result->task_score_value ?? ''), ENT_QUOTES, 'UTF-8');
                $attitudeNote = htmlspecialchars((string) ($result->attitude_note ?? ''), ENT_QUOTES, 'UTF-8');

                return '<button type="button" class="btn btn-sm btn-success saveGradeBtn" data-result-ids="' . $result->group_result_ids . '" disabled>Simpan</button> '
                    . '<button type="button" class="btn btn-sm btn-primary editGradeBtn" data-result-ids="' . $result->group_result_ids . '" data-student="' . $studentName . '" data-project="' . $projectName . '" data-material="' . $materialTitle . '" data-task-score="' . $taskScore . '" data-attitude-note="' . $attitudeNote . '" disabled>Edit</button> '
                    . '<button type="button" class="btn btn-sm btn-danger deleteGradeBtn" data-result-ids="' . $result->group_result_ids . '" disabled>Hapus</button>';
            })
            ->rawColumns(['student_name', 'project_name', 'pretest_score', 'posttest_score', 'task_score', 'average_score', 'attitude_note', 'action'])
            ->make(true);
    }

    public function exportGrades(Request $request)
    {
        $results = $this->buildGradeSummary($request);

        return Excel::download(
            new GradeExport($results),
            'nilai_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    private function buildGradeSummary(Request $request)
    {
        return Test_Result::with(['test.material.project', 'user', 'user.siswa.kelas'])
            ->join('tests', 'test_results.test_id', '=', 'tests.id')
            ->join('materials', 'tests.material_id', '=', 'materials.id')
            ->leftJoin('projects', 'materials.project_id', '=', 'projects.id')
            ->leftJoin('siswas', 'siswas.user_id', '=', 'test_results.user_id')
            ->join('project_members', 'project_members.user_id', '=', 'test_results.user_id')
            ->whereColumn('materials.project_id', 'project_members.project_id')
            ->when($request->filled('kelas_id'), function ($query) use ($request) {
                $kelasId = $request->kelas_id;
                $query->where('siswas.kelas_id', $kelasId);
            })
            ->when($request->filled('project_id'), function ($query) use ($request) {
                $projectId = $request->project_id;
                $query->where('materials.project_id', $projectId);
            })
            ->select(
                'test_results.*',
                'tests.type as test_type_raw',
                'projects.id as project_id',
                'projects.judul as project_name',
                'materials.id as material_id',
                'materials.title as material_title',
                'project_members.role_in_project as task_name',
                'siswas.kelas_id as kelas_id'
            )
            ->get()
            ->groupBy(function ($result) {
                return $result->user_id . ':' . $result->project_id . ':' . $result->material_id;
            })
            ->map(function ($group) {
                $first = $group->first();
                $pretest = $group->firstWhere('test_type_raw', 'pretest');
                $posttest = $group->firstWhere('test_type_raw', 'posttest');
                $taskScore = $group->pluck('task_score')->filter(fn ($value) => $value !== null && $value !== '')->last();
                $attitudeNote = $group->pluck('attitude_note')->filter(fn ($value) => filled($value))->last();

                $first->pretest_score = $pretest?->manual_score ?? $pretest?->score;
                $first->posttest_score = $posttest?->manual_score ?? $posttest?->score;
                $first->task_score_value = $taskScore;
                $first->average_score = $this->calculateAverageScore([
                    $first->pretest_score,
                    $first->posttest_score,
                    $taskScore,
                ]);
                $first->group_result_ids = $group->pluck('id')->implode(',');
                $first->attitude_note = $attitudeNote;

                return $first;
            })
            ->values();
    }

    public function gradeUpdate(Request $request)
    {
        $validated = $request->validate([
            'result_ids' => 'required|string',
            'manual_score' => 'nullable|numeric|min:0|max:100',
            'task_score' => 'nullable|numeric|min:0|max:100',
            'attitude_note' => 'nullable|string|max:2000',
        ]);

        $resultIds = collect(explode(',', $validated['result_ids']))
            ->map(fn ($value) => (int) trim($value))
            ->filter()
            ->values();

        Test_Result::whereIn('id', $resultIds)->update([
            'task_score' => $validated['task_score'],
            'attitude_note' => $validated['attitude_note'],
        ]);

        return response()->json(['message' => 'Nilai berhasil disimpan']);
    }

    public function gradeDestroy(Request $request)
    {
        $validated = $request->validate([
            'result_ids' => 'required|string',
        ]);

        $resultIds = collect(explode(',', $validated['result_ids']))
            ->map(fn ($value) => (int) trim($value))
            ->filter()
            ->values();

        Test_Result::whereIn('id', $resultIds)->delete();

        return response()->json(['message' => 'Nilai berhasil dihapus']);
    }

    private function formatGradeValue($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . '%';
    }

    private function calculateAverageScore(array $scores)
    {
        $values = collect($scores)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (float) $value)
            ->values();

        if ($values->isEmpty()) {
            return null;
        }

        return round($values->avg(), 2);
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
