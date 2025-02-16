<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Note;
use App\Models\Moment;
use App\Models\Video;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // NoteController.php
    public function index(Request $request)
    {
        // このリクエストにはtag, sort, directionのパラメータが含まれているという想定だが、初期の状態ではパラメータは含まれていないので、コード減らせそう
        $query = Note::query();
        if ($request->input('tab') === 'my-notes') {
            $query->where('user_id', auth()->id());
        }

        if ($request->has('tags') && !empty($request->tags)) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);
    
        $initialNotes = $query->with('tags')
            ->latest()
            ->limit(12)
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'thumbnail' => $note->thumbnail,
                    'created_at' => $note->created_at,
                    'tags' => $note->tags,
                    'user_id' => $note->user_id 
                ];
            });
    
        // このタグ必要？？？？
        $tags = Tag::where('user_id', auth()->id())
                ->get();
    
        return Inertia::render('Note/Index', [
            'initialNotes' => $initialNotes,
            'filters' => $request->only(['tag', 'sort', 'direction']),
            'tags' => $tags
        ]);
    }

    public function apiIndex(Request $request)
    {
        $query = Note::query();
        
        if ($request->input('tab') === 'my-notes') {
            $query->where('user_id', auth()->id());
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        return response()->json(
            $query->with('tags')
                ->paginate(12)
                ->through(function ($note) {
                    return [
                        'id' => $note->id,
                        'title' => $note->title,
                        'created_at' => $note->created_at,
                        'tags' => $note->tags,
                        'user_id' => $note->user_id
                    ];
                })
        );
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
            'thumbnail' => $video['thumbnail'],
        ]);
        // return Inertia::location(route('notes.show', ['note' => $note->id]));
        return redirect()->route('notes.show', ['note' => $note->id]);  
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = Note::findOrFail($id);
        $moments = Moment::where('note_id', $id)
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
