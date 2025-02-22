<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExploreVideo extends Model
{
    use HasFactory;

    protected $fillable = ['youtubeVideo_id', 'video_resource', 'category'];
    protected $casts = [
        'video_resource' => 'array',
    ];
}
