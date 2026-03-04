<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'name',
        'description',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
