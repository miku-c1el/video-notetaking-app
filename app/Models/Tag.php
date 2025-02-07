<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Note;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class)->withTimestamps()->withPivot('created_at');
    }
}
