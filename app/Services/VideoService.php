<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\CachedSearchResult;
use Carbon\Carbon;

class VideoService
{
    protected $baseUrl;
    protected $apiKey;
    protected $type;
    protected $part;
    protected $fields;

    public function __construct()
    {
        $this->baseUrl = config('services.youtube.base_url');
        $this->apiKey = config('services.youtube.api_key');
        $this->type = 'video';
        $this->part = 'snippet';
    }

    public function searchVideos(string $query, int $maxResults = 50)
    {
        try {
            $url = "{$this->baseUrl}/search";
            
            $response = Http::get($url, [
                'q' => $query,
                'part' => $this->part,
                'key' => $this->apiKey,
                'type' => $this->type,
                'maxResults' => $maxResults,
            ]);

            // レスポンスが成功したか確認
            if (!$response->successful()) {
                Log::error('YouTube API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $videos = $response->json()['items'] ?? [];

            if (!empty($videos)) {
                CachedSearchResult::updateOrCreate(
                    ['query' => $query],
                    [
                        'query' => $query,
                        'result' => json_encode($videos),
                        'cached_at' => Carbon::now('Asia/Tokyo'),
                        'expires_at' => Carbon::now('Asia/Tokyo')->addDay(1)
                    ]
                );
            }

            return $this->formatVideos($videos);

        } catch (\Exception $e) {
            Log::error('Error in searchVideos', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);
            return [];
        }
    }

    public function formatVideos($videos)
    {
        return collect($videos)->map(function ($video) {
            try {
                // 必要なキーの存在を確認
                if (!isset($video['id']['videoId']) ||
                    !isset($video['snippet']['title']) ||
                    !isset($video['snippet']['publishedAt']) ||
                    !isset($video['snippet']['thumbnails']['high']['url']) ||
                    !isset($video['snippet']['channelTitle'])) {
                    print($video['id']['videoId']);
                    print('id: '. $video['id']['videoId']);
                    print('title: '. $video['snippet']['title']);
                    print('published_at : '. $video['snippet']['publishedAt']);
                    print('thum: '. $video['snippet']['thumbnails']['high']['url']);
                    return null;
                }

                return [
                    'videoId' => $video['id']['videoId'],
                    'title' => $video['snippet']['title'],
                    'publishedAt' => $video['snippet']['publishedAt'],
                    'thumbnail' => $video['snippet']['thumbnails']['high']['url'],
                    'channelTitle' => $video['snippet']['channelTitle'],
                ];
            } catch (\Exception $e) {
                Log::error('Error formatting video', [
                    'error' => $e->getMessage(),
                    'video' => $video
                ]);
                return null;
            }
        })
        ->filter() // null値を除去
        ->values() // 配列のインデックスを振り直し
        ->toArray();
    }

//※ 返される値の例
    // [{
    //     "videoId": "yZt4ZOy6Z8c",
    //     "title": "How to Find Your Purpose &amp; Design the Life You Want",
    //     "publishedAt": "2025-01-23T10:01:04Z",
    //     "thumbnail": "https://i.ytimg.com/vi/yZt4ZOy6Z8c/hqdefault.jpg",
    //     "channelTitle": "Mel Robbins"
    //     }]
}