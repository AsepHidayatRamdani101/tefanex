<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Project;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'content',
        'file_path',
        'created_by',
        'project_id',
        'video_link',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}