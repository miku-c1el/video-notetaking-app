<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'note_id',
        'title',
        'content',
        'timestamp'
    ];
}
