<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTestAnswer extends Model
{
    use HasFactory;

    protected $table = 'student_test_answers';

    protected $fillable = [
        'test_result_id',
        'question_id',
        'answer_text',
        'selected_option',
        'is_correct',
    ];

    public function testResult()
    {
        return $this->belongsTo(Test_Result::class, 'test_result_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
