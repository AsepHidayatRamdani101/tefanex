<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test_Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'user_id',
        'score',
    ];

    protected $table = 'test_results';

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'user_id', 'user_id');
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentTestAnswer::class);
    }
}
