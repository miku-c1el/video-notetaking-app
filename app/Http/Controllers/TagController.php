<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
use App\Models\Note;
use Inertia\Inertia;


class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $note_id = $request->get('note_id');
        $note = Note::find($note_id);
        $tags = $note->tags()->orderBy('created_at')->get();
        return response()->json([
            'tags' => $tags
        ]);
    }

    /**
     * Search Tags
    */

    public function search(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Forbidden');
        }
        
        $query = trim(strip_tags($request->input('query', '')));
        
        $tags = Tag::where('user_id', auth()->id())
            ->where('name', 'like', "%{$query}%")
            ->take(5)
            ->get();

        return response()->json(['tags' => $tags]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'note_id' => 'required|exists:notes,id'
        ]);

        $tag = Tag::where('name', $validated['name'])->where('user_id', Auth::id())->first();

        $tag = Tag::updateOrCreate(
        ['name' => trim(strip_tags($validated['name'])), 'user_id' => Auth::id()],
        [
            'name' => trim(strip_tags($validated['name'])),
            'user_id' => Auth::id()
        ]);

        $note = Note::find($validated['note_id']);

        try {
            $note->tags()->syncWithoutDetaching([$tag->id]);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'This tag is already attached to the note.');
            }
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $name = trim(strip_tags($request->input('name')));
        $tag = Tag::findOrFail($id);
        $this->authorize('update', $tag);
        $tag->name = $name;
        $tag->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        $tag = Tag::findOrFail($id);
        $this->authorize('delete', $tag);
        Tag::destroy($id);
        return redirect()->back();
    }
}
