<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Note;
use App\Models\Moment;
use App\Models\Video;
use App\Models\Tag;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Note::query()->where('user_id', auth()->id());

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        $initialNotes = $query->with('tags')
        ->limit(12)
        ->get();

        $tags = Tag::all();

        return Inertia::render('Note/Index', [
            'initialNotes' => $initialNotes,
            'filters' => $request->only(['tag', 'sort', 'direction']),
            'tags' => $tags
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

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
        return Inertia::location(route('notes.show', ['noteId' => $note->id]));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $noteId)
    {
        $note = Note::findOrFail($noteId);
        $moments = Moment::where('note_id', $noteId)
            ->orderBy('timestamp', 'asc')
            ->get();
        return Inertia::render('Note/Create', [
            'note' => $note, 
            'moments' => $moments
        ]);
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
        $title = trim(strip_tags($request->input('title')));

        $note = Note::findOrFail($id);
        $note->title = $title;
        $note->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', $note);
        $note->delete();
        return redirect()->back();
    }
}
