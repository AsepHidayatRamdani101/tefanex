<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'guru_id',
        'client',
        'status',
        'deskripsi',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
