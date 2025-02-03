<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\User;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'youtubeVideo_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }    

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'youtubeVideo_id', 'youtubeVideo_id');
    }
}
