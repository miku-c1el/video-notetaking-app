<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteApiController extends Controller
{
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

        return $query->with('tags')
            ->paginate(12)
            ->through(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'created_at' => $note->created_at,
                    'tags' => $note->tags,
                ];
            });
    }
}