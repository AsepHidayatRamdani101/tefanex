<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design_Brief extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'description',
        'approval_status',
        'approved_by',
    ];

    protected $table = 'design_briefs';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


}
