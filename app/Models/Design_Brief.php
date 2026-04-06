<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design_Brief extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'design_briefs';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function mockup()
    {
        return $this->hasMany(Mockup::class);
    }

    public function timeline()
    {
        return $this->hasOne(Timeline::class, 'project_id', 'project_id');
    }

        public function massProduction()
        {
            return $this->hasOne(Mass_Production::class, 'project_id', 'project_id');
        }


}
