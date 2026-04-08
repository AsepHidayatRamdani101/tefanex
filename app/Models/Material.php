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

    /**
     * Convert YouTube URL to embeddable format
     */
    public function getEmbedVideoLinkAttribute()
    {
        if (!$this->video_link) {
            return null;
        }

        $link = $this->video_link;

        // Handle youtube.com/watch?v=ID format
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtube\.com\/\?v=)([a-zA-Z0-9_-]{11})/', $link, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Handle youtu.be/ID format
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $link, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Handle already embed format
        if (strpos($link, 'youtube.com/embed/') !== false) {
            return $link;
        }

        // If it's any other format, return as is
        return $link;
    }
}