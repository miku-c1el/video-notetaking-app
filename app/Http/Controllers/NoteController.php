<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Note;
use App\Models\Video;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $noteId)
    {
        $note = Note::findOrFail($noteId);
        return Inertia::render('Notes/Create', ['note' => $note]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $video = $request->input('video');
        Video::updateOrCreate(
            ['youtubeVideo_id' => $video['videoId']],
            [
                'youtubeVideo_id' => $video['videoId'],
                'video_resource' => json_encode($video),
                'cached_at' => Carbon::now('Asia/Tokyo'),
                'expires_at' => Carbon::now('Asia/Tokyo')->addDay(1)
            ]
        );

        $note = Note::create([
            'title' => $video['title'],
            'user_id' => auth()->id(),
            'youtubeVideo_id' => $video['videoId'],
        ]);

        return Inertia::location(route('notes.create', ['noteId' => $note->id]));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

// networkを見たところ、payloadにnoteが渡されてない？そもそもgetやのにrequestってなくない？createメソッドの話
