<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'role_in_project',
        

    ];

    protected $table = 'project__members';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function design_briefs()
    {
        return $this->hasMany(Design_Brief::class, 'project_id', 'project_id');
    }
}
