<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design_Brief extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'budget' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'quantity' => 'integer',
        'reference_files' => 'array',
    ];

    protected $appends = [
        'reference_files_list',
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

        public function getReferenceFilesListAttribute()
        {
            if (is_array($this->reference_files) && !empty($this->reference_files)) {
                return $this->reference_files;
            }

            if (!empty($this->reference_file)) {
                return [$this->reference_file];
            }

            return [];
        }


}
