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

    public function designBrief()
    {
        return $this->hasOne(Design_Brief::class);
    }

    public function timeline()
    {
        return $this->hasOne(Timeline::class);
    }

    public function mockups()
    {
        return $this->hasMany(Mockup::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function qualityControls()
    {
        return $this->hasMany(Quality_Control::class);
    }

    // tambahkan relasi mass production
    public function massProductions()
    {
        return $this->hasMany(Mass_Production::class);
    }
}
