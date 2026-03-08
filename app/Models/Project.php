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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project_members()
    {
        return $this->hasMany(Project_Member::class);
    }
}
