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

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($moment) {
            $moment->note->touch();
        });

        static::deleting(function ($moment) {
            $moment->note->touch(); 
        });
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }    
}
