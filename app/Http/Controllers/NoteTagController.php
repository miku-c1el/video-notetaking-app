<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Note $note)
    {
        $validated = $request->validate([
            'tag_id' => 'required|exists:tags,id'
        ]);

        $note->tags()->attach($validated['tag_id']);

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note, string $tag_id)
    {
        $note->tags()->detach($tag_id);

        return redirect()->back();
    }
}
