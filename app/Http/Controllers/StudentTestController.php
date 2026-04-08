<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Test_Result;
use App\Models\StudentTestAnswer;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentTestController extends Controller
{
    /**
     * Display a listing of available tests for the student
     */
    public function listTests()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('dashboard')
                ->with('error', 'Data siswa tidak ditemukan. Hubungi administrator.');
        }

        // Get all available tests (pretest and posttest)
        $tests = Test::with('material')
            ->get()
            ->map(function ($test) use ($siswa, $user) {
                $result = Test_Result::where('test_id', $test->id)
                    ->where('user_id', $user->id)
                    ->first();

                $test->completed = $result ? true : false;
                $test->score = $result ? $result->score : null;
                $test->completed_at = $result ? $result->created_at : null;
                $test->testResult = $result;

                return $test;
            });

        return view('student.tests.list', compact('tests', 'siswa'));
    }

    /**
     * Display the test for student to work on
     */
    public function showTest(Test $test)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();

        $test = $test->load(['material', 'questions.answers']);

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

        // Process each answer
        foreach ($test->questions as $question) {
            $answerKey = 'answer_' . $question->id;
            $userAnswer = $request->input($answerKey);

            $isCorrect = false;

            if ($question->type === 'multiple_choice') {
                // Check if selected option is correct
                if ($userAnswer !== null) {
                    $correctAnswer = $question->answers()->where('is_correct', true)->first();
                    if ($correctAnswer && $correctAnswer->id == $userAnswer) {
                        $isCorrect = true;
                        $correctAnswers++;
                    }
                }
            } else if ($question->type === 'essay') {
                // Essay answers need manual grading, store the answer
                $isCorrect = false;
            }

            // Store student answer
            StudentTestAnswer::create([
                'test_result_id' => $testResult->id,
                'question_id' => $question->id,
                'answer_text' => $question->type === 'essay' ? $userAnswer : null,
                'selected_option' => $question->type === 'multiple_choice' ? $userAnswer : null,
                'is_correct' => $isCorrect,
            ]);
        }

        // Calculate score (percentage)
        $score = ($correctAnswers / $totalQuestions) * 100;
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
        $user = Auth::user();
        
        // Make sure the test result belongs to the authenticated user
        if ($result->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $testResult = $result->load(['test.questions.answers', 'studentAnswers']);
        $siswa = Siswa::where('user_id', $user->id)->first();

        return view('student.tests.result', compact('testResult', 'siswa'));
    }
}
