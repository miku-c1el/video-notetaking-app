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

            // クォータ超過エラーの検出（HTTP 403 + quotaExceeded）
            if ($response->status() === 403) {
                $body = $response->json();
                if (isset($body['error']['errors'][0]['reason']) && 
                    $body['error']['errors'][0]['reason'] === 'quotaExceeded') {
                    Log::error('YouTube API quota exceeded', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    throw new \Exception('YouTube API quota exceeded', 429);
                }
            }

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
                'code' => $e->getCode(),
                'query' => $query
            ]);

            // クォータ超過エラーを上位に伝播
            if ($e->getCode() === 429) {
                throw $e;
            }
            
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

    public function formatExploreVideo($video)
    {
        return [
            'videoId' => $video['id'],
            'title' => $video['snippet']['title'],
            'publishedAt' => $video['snippet']['publishedAt'],
            'thumbnail' => $video['snippet']['thumbnails']['high']['url'],
            'channelTitle' => $video['snippet']['channelTitle'],
        ];
    }
}