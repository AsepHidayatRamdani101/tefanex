<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'principal_name',
        'principal_nip',
        'school_logo',
        'certificate_enabled',
        'certificate_title',
        'certificate_subtitle',
        'certificate_footer',
        'certificate_number',
        'certificate_place',
        'certificate_template',
    ];
}
