<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VideoService;
use App\Models\Video;
use App\Models\CachedSearchResult;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class VideoController extends Controller
{
    protected $videoService;
    protected $cachedSearchResult;

    public function __construct(VideoService $videoService, CachedSearchResult $cachedSearchResult)
    {
        $this->videoService = $videoService;
        $this->cachedSearchResult = $cachedSearchResult;
    }

    public function index(Request $request): Response
    {
        $query = trim(strip_tags($request->input('query', '')));

        if (!empty($query)) {
            $videos_json = $this->cachedSearchResult->where('query', $query)->where('expires_at', '>=', Carbon::now('Asia/Tokyo'))->first();
            
            if ($videos_json) {
                $videos = json_decode($videos_json['result'], true);
                $videos = $this->videoService->formatVideos($videos);

            } else {
                try {
                    $videos = $this->videoService->searchVideos($query);
                } catch (\Exception $e) {
                    if ($e->getCode() === 429) {
                        return Inertia::render('Errors/QuotaExceeded', [
                            'message' => '検索リクエストの上限に達しました。しばらくしてからお試しください。'
                        ]);
                    }
                    $videos = [];
                }
            }

        } else {
            $videos = [];
        }
        
        return Inertia::render('Video/Search', [
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

        try {
            $videos = $this->videoService->searchVideos($query);
            return response()->json($videos);
        } catch (\Exception $e) {
            if ($e->getCode() === 429) {
                return response()->json([
                    'error' => 'quota_exceeded',
                    'message' => '検索リクエストの上限に達しました。しばらくしてからお試しください。'
                ], 429);
            }
            
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}