<?php

namespace App\Http\Controllers;

use App\Models\Moment;
use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MomentController extends Controller
{

    public function index(Request $request)
    {
        $note_id = $request->input('note_id');
        $note = Note::findOrFail($note_id);
        $moments = Moment::where('note_id', $note_id)
            ->orderBy('timestamp', 'asc')
            ->get();

        return response()->json([
            'moments' => $moments
        ]);
    }

    public function store(Request $request)
    {
        $timestamp = $request->input('timestamp');
        $note_id = $request->input('noteId');
        $title = trim(strip_tags($request->input('title')));
        $content = trim(strip_tags($request->input('content')));

        $note = Note::findOrFail($note_id);
        
        $this->authorize('create', [Moment::class, $note]);

        $moment = Moment::updateOrCreate(
            ['note_id' => $note_id, 'timestamp' => $timestamp],
            [
                'note_id' => $note_id,
                'title' => $title,
                'content' => $content,
                'timestamp' => $timestamp
            ]
        );

        return response()->json([
            'moment' => $moment,
            'timestamp' => $timestamp
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(string $noteId)
    {
    }


    /**
     * Update the specified resource in storage.
    */
    public function update(Request $request, string $id)
    {
        $title = trim(strip_tags($request->input('title')));
        $content = trim(strip_tags($request->input('content')));
        $moment = Moment::find($id);
        $this->authorize('update', $moment);
        $moment->title = $title;
        $moment->content = $content;
        $moment->save();
        return response()->json([
            'moment' => $moment,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $moment_id)
    {
        $moment = Moment::findOrFail($moment_id);
        $this->authorize('delete', $moment);
        Moment::destroy($moment_id);
        return response()->json(['success' => true]);
    }
}