<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Test;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $testId;
    protected $errors = [];
    protected $imported = 0;

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $questionData = [
                    'test_id' => $this->testId,
                    'question_text' => $row['pertanyaan'] ?? $row['question'] ?? '',
                    'type' => $this->determineQuestionType($row),
                ];

                if ($questionData['type'] === 'multiple_choice') {
                    $options = $this->extractOptions($row);
                    if (count($options) >= 2 && count($options) <= 5) {
                        $questionData['options'] = $options;
                        $questionData['correct_answer'] = $this->extractCorrectAnswer($row, $options);
                    } else {
                        $this->errors[] = "Baris " . ($row->getIndex() + 1) . ": Jumlah pilihan harus 2-5 untuk soal pilihan ganda";
                        continue;
                    }
                } elseif ($questionData['type'] === 'essay') {
                    $questionData['correct_answer'] = $row['jawaban_benar'] ?? $row['correct_answer'] ?? null;
                }

                if (empty($questionData['question_text'])) {
                    $this->errors[] = "Baris " . ($row->getIndex() + 1) . ": Teks pertanyaan tidak boleh kosong";
                    continue;
                }

                Question::create($questionData);
                $this->imported++;

            } catch (\Exception $e) {
                $this->errors[] = "Baris " . ($row->getIndex() + 1) . ": " . $e->getMessage();
            }
        }
    }

    private function determineQuestionType($row)
    {
        // Check if there are multiple choice options
        $hasOptions = false;
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($row['pilihan_' . $i]) || !empty($row['option_' . $i])) {
                $hasOptions = true;
                break;
            }
        }

        return $hasOptions ? 'multiple_choice' : 'essay';
    }

    private function extractOptions($row)
    {
        $options = [];
        for ($i = 1; $i <= 5; $i++) {
            $option = $row['pilihan_' . $i] ?? $row['option_' . $i] ?? '';
            if (!empty($option)) {
                $options[] = trim($option);
            }
        }
        return $options;
    }

    private function extractCorrectAnswer($row, $options)
    {
        $correctAnswer = $row['jawaban_benar'] ?? $row['correct_answer'] ?? '';

        // If correct answer is a letter (A, B, C, D, E), convert to the actual option
        if (preg_match('/^[A-E]$/i', $correctAnswer)) {
            $index = ord(strtoupper($correctAnswer)) - ord('A');
            return $options[$index] ?? $correctAnswer;
        }

        return $correctAnswer;
    }

    public function rules(): array
    {
        return [
            'pertanyaan' => 'required|string|max:1000',
            'question' => 'nullable|string|max:1000',
            'pilihan_1' => 'nullable|string|max:255',
            'pilihan_2' => 'nullable|string|max:255',
            'pilihan_3' => 'nullable|string|max:255',
            'pilihan_4' => 'nullable|string|max:255',
            'pilihan_5' => 'nullable|string|max:255',
            'option_1' => 'nullable|string|max:255',
            'option_2' => 'nullable|string|max:255',
            'option_3' => 'nullable|string|max:255',
            'option_4' => 'nullable|string|max:255',
            'option_5' => 'nullable|string|max:255',
            'jawaban_benar' => 'nullable|string|max:1000',
            'correct_answer' => 'nullable|string|max:1000',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'pertanyaan.required' => 'Kolom pertanyaan wajib diisi',
            'pertanyaan.max' => 'Teks pertanyaan maksimal 1000 karakter',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getImportedCount()
    {
        return $this->imported;
    }
}
