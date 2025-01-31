<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VideoService
{
    protected $baseUrl;
    protected $apiKey;
    protected $type;
    protected $part;
    protected $fields;

    public function __construct()
    {
        $this->baseUrl = config('services.youtube.base_url');;
        $this->apiKey = config('services.youtube.api_key');;
        $this->type = 'video';
        $this->part = 'snippet';
    }

    public function searchVideos(string $query, int $maxResults = 50)
    {
        $url = "{$this->baseUrl}/search";
        
        $response = Http::get($url, [
            'q' => $query,
            'part' => $this->part,
            'key' => $this->apiKey,
            'type' => $this->type,
            'maxResults' => $maxResults
        ]);

        $videos = $response->json()['items'] ?? [];

        return collect($videos)->map(function ($video) {
            return [
                'videoId' => $video['id']['videoId'],
                'title' => $video['snippet']['title'],
                'publishedAt' => $video['snippet']['publishedAt'],
                'thumbnail' => $video['snippet']['thumbnails']['high']['url'],
                'channelTitle' => $video['snippet']['channelTitle'],
            ];
        })->toArray();
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