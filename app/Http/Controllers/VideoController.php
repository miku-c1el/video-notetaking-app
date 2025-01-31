<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VideoService;
use Inertia\Inertia;
use Inertia\Response;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function index(Request $request): Response
    {
        $query = $request->input('query', '');
        $videos = $query ? $this->videoService->searchVideos($query) : [];
        return Inertia::render('Videos/Search', [
            'videos' => $videos,
            'query' => $query
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        if(!$query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $videos = $this->videoService->searchVideos($query);
        return response()->json($videos);
    }


}