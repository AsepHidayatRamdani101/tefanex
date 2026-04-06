<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

    protected $table = 'timelines';

    protected $fillable = [
        'project_id',
        'start_date',
        'end_date',
        'created_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projectMember()
    {
        return $this->belongsTo(Project_Member::class);
    }

    public function designBrief()
    {
        return $this->belongsTo(Design_Brief::class);
    }

    public function mockup()
    {
        return $this->belongsTo(Mockup::class);
    }

    public function massProduction()
    {
        return $this->belongsTo(Mass_Production::class);
    }
}
