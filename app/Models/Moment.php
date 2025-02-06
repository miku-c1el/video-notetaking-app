<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Note;

class Moment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'note_id',
        'title',
        'content',
        'timestamp'
    ];

    protected $casts = [
        'timestamp' => 'integer',
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }    
}
