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
    private const PER_PAGE = 12;

    /**
     * Display a listing of the resource.
     */
    // NoteController.php
    public function index(Request $request)
    {
        $noteCount = Note::where('user_id', auth()->id())->count();
        $query = Note::where('user_id', auth()->id());
        
        if ($request->has('tags')) {
            $selected_tags = $request->tags;
            if (!empty($selected_tags)) {
                $query->whereHas('tags', function ($q) use ($selected_tags) {
                    $q->whereIn('name', $selected_tags);
                });
            }
        }

        $sort = $request->input('sort', 'created_at');

        $query->when($sort === 'created_at', function ($query) {
            return $query->orderBy('created_at', 'desc');
        })
        ->when($sort === 'last_opened', function ($query) {
            return $query->orderBy('updated_at', 'desc');
        });
    
        $paginator = $query->with('tags')
            ->latest()
            ->paginate(self::PER_PAGE);

        $all_tags = Tag::where('user_id', auth()->id())->get();
        return Inertia::render('Note/Index', [
            'initialNotes' => $paginator->through(fn($note) => [
                'id' => $note->id,
                'title' => $note->title,
                'thumbnail' => $note->thumbnail,
                'created_at' => $note->created_at,
                'tags' => $note->tags,
                'user_id' => $note->user_id
            ])->toArray(),
            'noteCount' => $noteCount,
            'tags' => $all_tags,
            'filters' => [
                'tags' => $request->input('tags', []),
                'sort' => (string) $request->input('sort', 'created_at')
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => self::PER_PAGE,
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
            ]
        ]);
    }

    public function apiIndex(Request $request)
    {
        try {
            $query = Note::where('user_id', auth()->id());

            if ($request->has('tags')) {
                $tags = json_decode($request->tags, true);
                if (!empty($tags)) {
                    $query->whereHas('tags', function ($q) use ($tags) {
                        $q->whereIn('name', $tags);
                    });
                }
            }

            $sort = $request->input('sort', 'created_at');
            $direction = $request->input('direction', 'desc');
            $query->orderBy($sort, $direction);

            $paginator = $query->with('tags')->paginate(self::PER_PAGE);

            return response()->json([
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'notes' => $paginator->items(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching notes'], 500);
        }
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
            'title' => trim(strip_tags($video['title'])),
            'user_id' => auth()->id(),
            'youtubeVideo_id' => $video['videoId'],
            'thumbnail' => $video['thumbnail'],
        ]);

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

        if (auth()->id() !== $note->user_id) {
            abort(403, '権限がありません');
        }
        $note->title = $title;
        $note->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $note = Note::findOrFail($id);

        $this->authorize('delete', $note);

        Note::destroy($id);
        return redirect()->back();
    }
}
