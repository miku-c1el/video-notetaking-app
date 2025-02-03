<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachedSearchResult extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'query',
        'result',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];    
}
