<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Test_Result;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of students for report selection
     */
    public function index()
    {
        $siswas = Siswa::with('kelas')->orderBy('nama')->get();
        $kelasList = Kelas::orderBy('name')->get();
        return view('reports.index', compact('siswas', 'kelasList'));
    }

    /**
     * Display the raport for a specific student
     */
    public function show(Siswa $siswa)
    {
        // Get all grades for this student grouped by project and material
        $grades = $this->getStudentGrades($siswa);

        // Get attendance summary for this student
        $attendance = $this->getAttendanceSummary($siswa);

        // Get school settings
        $schoolSetting = SchoolSetting::first();

        // Get teacher info (if available from project membership)
        $guru = null;

        return view('reports.raport', compact('siswa', 'grades', 'attendance', 'schoolSetting', 'guru'));
    }

    /**
     * Get all grades for a student grouped by project and material
     */
    private function getStudentGrades(Siswa $siswa)
    {
        if (!$siswa->user_id) {
            return collect();
        }

        $results = Test_Result::with(['test.material.project', 'user', 'user.siswa.kelas'])
            ->join('tests', 'test_results.test_id', '=', 'tests.id')
            ->join('materials', 'tests.material_id', '=', 'materials.id')
            ->leftJoin('projects', 'materials.project_id', '=', 'projects.id')
            ->where('test_results.user_id', $siswa->user_id)
            ->select(
                'test_results.*',
                'tests.type as test_type_raw',
                'projects.id as project_id',
                'projects.judul as project_name',
                'materials.id as material_id',
                'materials.title as material_title'
            )
            ->get()
            ->groupBy(function ($result) {
                return $result->project_id . ':' . $result->material_id;
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
                $first->attitude_note = $attitudeNote;

                return $first;
            })
            ->values()
            ->groupBy('project_name');

        return $results;
    }

    /**
     * Get attendance summary for a student
     */
    private function getAttendanceSummary(Siswa $siswa)
    {
        if (!$siswa->user_id) {
            return (object) [
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
            ];
        }

        $attendance = Attendance::where('user_id', $siswa->user_id)->get();

        return (object) [
            'sakit' => $attendance->where('status', 'sakit')->count(),
            'izin' => $attendance->where('status', 'izin')->count(),
            'alpa' => $attendance->where('status', 'alpa')->count(),
        ];
    }

    /**
     * Calculate average score from an array of scores
     */
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
     * Format grade value with percentage
     */
    private function formatGradeValue($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . '%';
    }
}
