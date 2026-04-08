<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Test_Result;
use App\Models\StudentTestAnswer;
use App\Models\Siswa;
use App\Models\Project_Member;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentTestController extends Controller
{
    /**
     * Display a listing of available tests for the student based on project membership
     */
    public function listTests()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('dashboard')
                ->with('error', 'Data siswa tidak ditemukan. Hubungi administrator.');
        }

        // Get projects where student is a member
        $projectIds = Project_Member::where('user_id', $user->id)
            ->pluck('project_id');

        // Get tests for materials in those projects
        $tests = Test::whereHas('material', function ($query) use ($projectIds) {
                $query->whereIn('project_id', $projectIds);
            })
            ->with(['material.project', 'questions.answers'])
            ->get()
            ->map(function ($test) use ($user) {
                $result = Test_Result::where('test_id', $test->id)
                    ->where('user_id', $user->id)
                    ->first();

                $test->completed = $result ? true : false;
                $test->score = $result ? $result->score : null;
                $test->completed_at = $result ? $result->created_at : null;
                $test->testResult = $result;

                return $test;
            });

        $projectTestGroups = $tests->filter(function ($test) {
                return $test->material && $test->material->project;
            })
            ->groupBy(function ($test) {
                return $test->material->project->id;
            })
            ->map(function ($group) {
                $project = $group->first()->material->project;
                return [
                    'project' => $project,
                    'tests' => $group,
                ];
            });

        return view('student.tests.list', compact('projectTestGroups', 'siswa'));
    }

    /**
     * Display the test for student to work on
     */
    public function showTest(Test $test)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();

        // Load material first
        $test->load(['material', 'questions.answers']);

        // Verify student has access to this test's project
        $projectIds = Project_Member::where('user_id', $user->id)->pluck('project_id');
        $material = $test->material;
        
        if (!$material || !$projectIds->contains($material->project_id)) {
            abort(403, 'Anda tidak memiliki akses ke test ini');
        }

        // Check if student already completed this test
        $existingResult = Test_Result::where('test_id', $test->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResult) {
            return redirect()->route('student.test.result', $existingResult->id)
                ->with('info', 'Anda sudah menyelesaikan test ini');
        }

        $questions = $test->questions;

        return view('student.tests.show', compact('test', 'siswa', 'questions'));
    }

    /**
     * Submit test answers
     */
    public function submitTest(Request $request, Test $test)
    {
        $user = Auth::user();
        $test = $test->load('questions.answers');

        // Load material for access verification
        $test->load('material');

        // Verify student has access to this test's project
        $projectIds = Project_Member::where('user_id', $user->id)->pluck('project_id');
        $material = $test->material;
        
        if (!$material || !$projectIds->contains($material->project_id)) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke test ini'], 403);
        }

        // Check if student already completed this test
        $existingResult = Test_Result::where('test_id', $test->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResult) {
            return response()->json(['message' => 'Anda sudah menyelesaikan test ini'], 422);
        }

        // Create test result
        $testResult = Test_Result::create([
            'test_id' => $test->id,
            'user_id' => $user->id,
            'score' => 0, // Will be calculated
        ]);

        $totalQuestions = $test->questions->count();
        $correctAnswers = 0;
        $totalAutoGraded = 0;

        // Process each answer
        foreach ($test->questions as $question) {
            $answerKey = 'answer_' . $question->id;
            $userAnswer = $request->input($answerKey);
            $isCorrect = false;
            $answerText = null;
            $selectedOption = null;

            if ($question->type === 'multiple_choice') {
                $selectedText = null;

                if ($userAnswer !== null) {
                    $selectedAnswer = is_numeric($userAnswer)
                        ? $question->answers->firstWhere('id', $userAnswer)
                        : null;

                    if ($selectedAnswer) {
                        $selectedText = $selectedAnswer->answer_text;
                        $selectedOption = (string) $selectedAnswer->id;
                    } else {
                        $selectedText = $userAnswer;
                        $selectedOption = $userAnswer;
                    }
                }

                if ($selectedText !== null) {
                    $answerText = $selectedText;
                    if ($question->correct_answer !== null && $selectedText === $question->correct_answer) {
                        $isCorrect = true;
                        $correctAnswers++;
                    }
                }

                $totalAutoGraded++;
            } elseif ($question->type === 'essay') {
                $answerText = $userAnswer;
                $selectedOption = null;
                $isCorrect = false;
            }

            StudentTestAnswer::create([
                'test_result_id' => $testResult->id,
                'question_id' => $question->id,
                'answer_text' => $answerText,
                'selected_option' => $selectedOption,
                'is_correct' => $isCorrect,
            ]);
        }

        // Calculate score based on auto-graded questions only
        $score = $totalAutoGraded > 0 ? ($correctAnswers / $totalAutoGraded) * 100 : 0;
        $testResult->update(['score' => round($score, 2)]);

        return response()->json([
            'message' => 'Test berhasil disubmit',
            'test_result_id' => $testResult->id,
            'score' => $testResult->score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ]);
    }

    /**
     * Display test result
     */
    public function showResult(Test_Result $result)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isTeacher = $user->hasRole('guru|super_admin|kepala_tefa');

        if (!$isTeacher) {
            // Make sure the test result belongs to the authenticated student
            if ($result->user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }

            // Verify student has access to this test's project
            $projectIds = Project_Member::where('user_id', $user->id)->pluck('project_id');
            $material = $result->test->material;
            
            if (!$material || !$projectIds->contains($material->project_id)) {
                abort(403, 'Anda tidak memiliki akses ke hasil test ini');
            }
        }

        $testResult = $result->load(['test.questions.answers', 'studentAnswers', 'test.material.project']);
        $siswa = Siswa::where('user_id', $result->user_id)->first();
        $taskScore = $testResult->task_score ?? $this->calculateTaskScore($testResult);
        $finalScore = $testResult->manual_score ?? $testResult->score;

        return view('student.tests.result', compact('testResult', 'siswa', 'isTeacher', 'taskScore', 'finalScore'));
    }

    public function updateEvaluation(Request $request, Test_Result $result)
    {
        $request->validate([
            'manual_score' => 'nullable|numeric|min:0|max:100',
            'task_score' => 'nullable|numeric|min:0|max:100',
            'attitude_note' => 'nullable|string|max:2000',
        ]);

        $result->update([
            'manual_score' => $request->input('manual_score'),
            'task_score' => $request->input('task_score'),
            'attitude_note' => $request->input('attitude_note'),
        ]);

        return redirect()->route('student.test.result', $result->id)
            ->with('success', 'Evaluasi guru berhasil disimpan.');
    }

    private function calculateTaskScore(Test_Result $testResult)
    {
        $project = $testResult->test->material?->project;
        if (!$project) {
            return null;
        }

        $member = Project_Member::where('user_id', $testResult->user_id)
            ->where('project_id', $project->id)
            ->first();

        if (!$member) {
            return null;
        }

        $role = strtolower($member->role_in_project);
        $taskItems = collect();

        if (in_array($role, ['marketing', 'marketing (pemasaran)'])) {
            if ($project->designBrief) {
                $taskItems->push([
                    'approved' => $project->designBrief->approval_status === 'approved',
                ]);
            }
        }

        if (in_array($role, ['designer', 'desain'])) {
            foreach ($project->mockups ?? collect() as $mockup) {
                $taskItems->push([
                    'approved' => $mockup->status === 'approved',
                ]);
            }
        }

        if (in_array($role, ['operator produksi', 'operator_produksi', 'produksi'])) {
            foreach ($project->productions ?? collect() as $production) {
                $taskItems->push([
                    'approved' => $production->status === 'selesai',
                ]);
            }
        }

        if (in_array($role, ['qc', 'quality control', 'quality_control', 'kontrol kualitas'])) {
            foreach ($project->qualityControls ?? collect() as $qc) {
                $taskItems->push([
                    'approved' => $qc->status === 'lulus',
                ]);
            }
        }

        $total = $taskItems->count();
        if ($total === 0) {
            return null;
        }

        $approvedCount = $taskItems->where('approved', true)->count();
        return round(($approvedCount / $total) * 100, 2);
    }
}
