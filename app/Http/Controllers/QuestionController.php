<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use App\Imports\QuestionsImport;
use App\Services\WordImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tests = Test::with('material')->orderBy('created_at', 'desc')->get();
        return view('question.index', compact('tests'));
    }

    public function data()
    {
        $questions = Question::with(['test.material'])->select('questions.*');

        return DataTables::of($questions)
            ->addColumn('test_info', function (Question $question) {
                $test = $question->test;
                $material = $test?->material;
                return $material ? $material->title . ' (' . ucfirst($test->type) . ')' : '-';
            })
            ->editColumn('type', function (Question $question) {
                $badge = match($question->type) {
                    'multiple_choice' => 'primary',
                    'essay' => 'success',
                    default => 'secondary'
                };
                $text = match($question->type) {
                    'multiple_choice' => 'Pilihan Ganda',
                    'essay' => 'Essay',
                    default => ucfirst($question->type)
                };
                return '<span class="badge badge-' . $badge . '">' . $text . '</span>';
            })
            ->editColumn('question_text', function (Question $question) {
                return strlen($question->question_text) > 50
                    ? substr($question->question_text, 0, 50) . '...'
                    : $question->question_text;
            })
            ->editColumn('created_at', function (Question $question) {
                return $question->created_at?->format('Y-m-d');
            })
            ->addColumn('action', function (Question $question) {
                return '<button class="btn btn-sm btn-primary editBtn" data-id="' . $question->id . '">Edit</button> '
                    . '<button class="btn btn-sm btn-danger deleteBtn" data-id="' . $question->id . '">Hapus</button>';
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
            'test_id' => 'required|exists:tests,id',
            'question_text' => 'required|string|max:1000',
            'type' => 'required|in:multiple_choice,essay',
            'options' => 'nullable|array|required_if:type,multiple_choice|min:2|max:5',
            'options.*' => 'string|max:255',
            'correct_answer' => 'nullable|string|max:1000',
        ]);

        // For multiple choice, ensure correct_answer is one of the options
        if ($validated['type'] === 'multiple_choice') {
            if (!in_array($validated['correct_answer'], $validated['options'])) {
                return response()->json(['message' => 'Jawaban benar harus salah satu dari pilihan yang tersedia'], 422);
            }
        }

        Question::create($validated);

        return response()->json(['message' => 'Pertanyaan berhasil dibuat']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Question::with('test.material')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Question::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'question_text' => 'required|string|max:1000',
            'type' => 'required|in:multiple_choice,essay',
            'options' => 'nullable|array|required_if:type,multiple_choice|min:2|max:5',
            'options.*' => 'string|max:255',
            'correct_answer' => 'nullable|string|max:1000',
        ]);

        // For multiple choice, ensure correct_answer is one of the options
        if ($validated['type'] === 'multiple_choice') {
            if (!in_array($validated['correct_answer'], $validated['options'])) {
                return response()->json(['message' => 'Jawaban benar harus salah satu dari pilihan yang tersedia'], 422);
            }
        }

        $question->update($validated);

        return response()->json(['message' => 'Pertanyaan berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Pertanyaan berhasil dihapus']);
    }

    /**
     * Import questions from Excel file
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|max:10240', // 10MB max
            'test_id' => 'required|exists:tests,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->hasFile('excel_file')) {
                $extension = strtolower($request->file('excel_file')->getClientOriginalExtension());
                if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
                    $validator->errors()->add('excel_file', 'The excel file field must be a file of type: xlsx, xls, csv.');
                }
            }
        });

        $validator->validate();

        try {
            $import = new QuestionsImport($request->test_id);
            Excel::import($import, $request->file('excel_file'));

            $imported = $import->getImportedCount();
            $errors = $import->getErrors();

            if ($imported > 0) {
                $message = "Berhasil mengimport {$imported} pertanyaan";
                if (!empty($errors)) {
                    $message .= ". Beberapa error: " . implode('; ', array_slice($errors, 0, 3));
                }
                return response()->json(['message' => $message, 'imported' => $imported, 'errors' => $errors]);
            } else {
                return response()->json(['message' => 'Tidak ada pertanyaan yang berhasil diimport. Errors: ' . implode('; ', $errors)], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error importing file: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Import questions from Word file
     */
    public function importWord(Request $request)
    {
        $request->validate([
            'word_file' => 'required|file|mimes:doc,docx|max:10240', // 10MB max
            'test_id' => 'required|exists:tests,id',
        ]);

        try {
            $file = $request->file('word_file');
            $filePath = $file->store('temp');

            $importService = new WordImportService($request->test_id);
            $importService->importFromFile(storage_path('app/' . $filePath));

            // Clean up temp file
            \Illuminate\Support\Facades\Storage::delete($filePath);

            $imported = $importService->getImportedCount();
            $errors = $importService->getErrors();

            if ($imported > 0) {
                $message = "Berhasil mengimport {$imported} pertanyaan dari Word";
                if (!empty($errors)) {
                    $message .= ". Beberapa error: " . implode('; ', array_slice($errors, 0, 3));
                }
                return response()->json(['message' => $message, 'imported' => $imported, 'errors' => $errors]);
            } else {
                return response()->json(['message' => 'Tidak ada pertanyaan yang berhasil diimport. Errors: ' . implode('; ', $errors)], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error importing Word file: ' . $e->getMessage()], 500);
        }
    }
}
