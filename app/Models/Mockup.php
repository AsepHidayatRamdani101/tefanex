<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mockup extends Model
{
    use HasFactory;

    protected $table = 'mockups';

    protected $fillable = [
        'project_id',
        'file_path',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designBrief()
    {
        return $this->belongsTo(Design_Brief::class);
    }

    public function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }

}
