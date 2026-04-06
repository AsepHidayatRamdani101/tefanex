<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mass_Production extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'mass_productions';

    

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function timeline()
    {
        return $this->hasOne(Timeline::class, 'project_id', 'project_id');
    }

    public function designBrief()
    {
        return $this->hasOne(Design_Brief::class, 'project_id', 'project_id');
    }
}
