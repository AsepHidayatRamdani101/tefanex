<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produksi',
        'deskripsi',
        'status',
        'hasil_produksi',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function projectMembers()
    {
        return $this->hasMany(Project_Member::class);
    }

    
}
