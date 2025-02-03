<?php

namespace App\Http\Controllers;

use App\Models\Moment;
use Illuminate\Http\Request;

class MomentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'video_id' => 'required|exists:videos,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'timestamp' => 'required|integer|min:0'
        ]);

        $moment = Moment::create([
            ...$validated,
            'note_id' => $request->note_id,
            'user_id' => auth()->id()
        ]);

        return redirect()->back();
    }
}