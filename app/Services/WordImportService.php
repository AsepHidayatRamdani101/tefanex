<?php

namespace App\Services;

use App\Models\Question;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use Illuminate\Support\Facades\Storage;

class WordImportService
{
    protected $testId;
    protected $errors = [];
    protected $imported = 0;

    public function __construct($testId)
    {
        $this->testId = $testId;
    }

    public function importFromFile($filePath)
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $questions = $this->parseDocument($phpWord);

            foreach ($questions as $questionData) {
                try {
                    if (empty($questionData['question_text'])) {
                        $this->errors[] = "Teks pertanyaan tidak boleh kosong";
                        continue;
                    }

                    Question::create($questionData);
                    $this->imported++;
                } catch (\Exception $e) {
                    $this->errors[] = "Error creating question: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $this->errors[] = "Error reading Word file: " . $e->getMessage();
        }
    }

    private function parseDocument($phpWord)
    {
        $questions = [];
        $currentQuestion = null;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    $text = trim($element->getText());
                    if (!empty($text)) {
                        $this->processText($text, $questions, $currentQuestion);
                    }
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $this->processTable($element, $questions, $currentQuestion);
                }
            }
        }

        // Finalize the last question
        if ($currentQuestion) {
            $questions[] = $this->finalizeQuestion($currentQuestion);
        }

        return $questions;
    }

    private function processText($text, &$questions, &$currentQuestion)
    {
        // Check if this is a question (starts with number followed by dot)
        if (preg_match('/^\d+\.\s*(.+)$/', $text, $matches)) {
            // Save previous question if exists
            if ($currentQuestion) {
                $questions[] = $this->finalizeQuestion($currentQuestion);
            }

            // Start new question
            $currentQuestion = [
                'test_id' => $this->testId,
                'question_text' => trim($matches[1]),
                'type' => 'essay', // default
                'options' => [],
                'correct_answer' => null
            ];
        }
        // Check if this is an option (A., B., C., etc.)
        elseif ($currentQuestion && preg_match('/^[A-E]\.\s*(.+)$/i', $text, $matches)) {
            $currentQuestion['type'] = 'multiple_choice';
            $currentQuestion['options'][] = trim($matches[1]);
        }
        // Check if this is correct answer indicator
        elseif ($currentQuestion && preg_match('/^Jawaban:\s*(.+)$/i', $text, $matches)) {
            $currentQuestion['correct_answer'] = trim($matches[1]);
        }
    }

    private function processTable($table, &$questions, &$currentQuestion)
    {
        // Process table format (each row is a question)
        foreach ($table->getRows() as $row) {
            $cells = $row->getCells();
            if (count($cells) >= 2) {
                $questionText = trim($cells[0]->getText());
                if (!empty($questionText)) {
                    // Save previous question
                    if ($currentQuestion) {
                        $questions[] = $this->finalizeQuestion($currentQuestion);
                    }

                    $currentQuestion = [
                        'test_id' => $this->testId,
                        'question_text' => $questionText,
                        'type' => 'essay',
                        'options' => [],
                        'correct_answer' => null
                    ];

                    // Check for options in subsequent cells
                    for ($i = 1; $i < count($cells); $i++) {
                        $cellText = trim($cells[$i]->getText());
                        if (!empty($cellText)) {
                            $currentQuestion['type'] = 'multiple_choice';
                            $currentQuestion['options'][] = $cellText;
                        }
                    }

                    // Last cell might be correct answer
                    if (count($cells) > 2 && !empty($cells[count($cells)-1]->getText())) {
                        $lastCell = trim($cells[count($cells)-1]->getText());
                        if (!in_array($lastCell, $currentQuestion['options'])) {
                            $currentQuestion['correct_answer'] = $lastCell;
                        }
                    }
                }
            }
        }
    }

    private function finalizeQuestion($question)
    {
        // Validate options count for multiple choice
        if ($question['type'] === 'multiple_choice') {
            $optionCount = count($question['options']);
            if ($optionCount < 2 || $optionCount > 5) {
                $question['type'] = 'essay'; // Fallback to essay if invalid option count
                $question['options'] = null;
            }

            // If no correct answer specified, assume first option
            if (empty($question['correct_answer']) && !empty($question['options'])) {
                $question['correct_answer'] = $question['options'][0];
            }
        }

        return $question;
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