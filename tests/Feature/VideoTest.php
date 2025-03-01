<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\CachedSearchResult;
use App\Services\VideoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Exception;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->videoService = Mockery::mock(VideoService::class);
    $this->formattedVideos = [
        [
            'videoId' => 'WJ3-F02-F_Y',
            'title' => 'The Most Beautiful & Relaxing Piano Pieces (Vol. 1)',
            'publishedAt' => '2019-04-13T13:00:04Z',
            'thumbnail' => 'https://i.ytimg.com/vi/WJ3-F02-F_Y/hqdefault.jpg',
            'channelTitle' => 'Rousseau',
        ]
    ];

    $this->videoService = $this->mock(VideoService::class, function ($mock) {
        $mock->shouldReceive('formatVideos')
            ->andReturn($this->formattedVideos);
    });
        
    $this->cachedSearchResult = CachedSearchResult::factory()->create([
        'query' => 'Laravel',
        'result' => json_encode([
            [
                "id" => ["videoId" => "WJ3-F02-F_Y"],
                "snippet" => [
                    "title" => "The Most Beautiful &amp; Relaxing Piano Pieces (Vol. 1)",
                    "publishedAt" => "2019-04-13T13:00:04Z",
                    "thumbnails" => ["high" => ["url" => "https://i.ytimg.com/vi/WJ3-F02-F_Y/hqdefault.jpg"]],
                    "channelTitle" => "Rousseau",
                ]
            ],
            [
                "id" => ["videoId" => "WJ3-F02-F_Y"],
                "snippet" => [
                    "title" => "The Most Beautiful &amp; Relaxing Piano Pieces (Vol. 1)",
                    "publishedAt" => "2019-04-13T13:00:04Z",
                    "thumbnails" => ["high" => ["url" => "https://i.ytimg.com/vi/WJ3-F02-F_Y/hqdefault.jpg"]],
                    "channelTitle" => "Rousseau",
                ]
            ],
            [
                "id" => ["videoId" => "WJ3-F02-F_Y"],
                "snippet" => [
                    "title" => "The Most Beautiful &amp; Relaxing Piano Pieces (Vol. 1)",
                    "publishedAt" => "2019-04-13T13:00:04Z",
                    "thumbnails" => ["high" => ["url" => "https://i.ytimg.com/vi/WJ3-F02-F_Y/hqdefault.jpg"]],
                    "channelTitle" => "Rousseau",
                ]
            ],
            [
                "id" => ["videoId" => "WJ3-F02-F_Y"],
                "snippet" => [
                    "title" => "The Most Beautiful &amp; Relaxing Piano Pieces (Vol. 1)",
                    "publishedAt" => "2019-04-13T13:00:04Z",
                    "thumbnails" => ["high" => ["url" => "https://i.ytimg.com/vi/WJ3-F02-F_Y/hqdefault.jpg"]],
                    "channelTitle" => "Rousseau",
                ]
            ],
            [
                "id" => ["videoId" => "WJ3-F02-F_Y"],
                "snippet" => [
                    "title" => "The Most Beautiful &amp; Relaxing Piano Pieces (Vol. 1)",
                    "publishedAt" => "2019-04-13T13:00:04Z",
                    "thumbnails" => ["high" => ["url" => "https://i.ytimg.com/vi/WJ3-F02-F_Y/hqdefault.jpg"]],
                    "channelTitle" => "Rousseau",
                ]
            ],
        ]),
        'expires_at' => Carbon::now('Asia/Tokyo')->addDay(1),
    ]);

});

test('動画検索（キャッシュあり）のテスト', function () {
    $response = $this->actingAs($this->user)->get(route('videos.index', [
        'query' => 'Laravel'
    ]));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Video/Search')
            ->has('videos', 1)
            ->where('query', 'Laravel')
    );
});

test('動画検索（キャッシュなし）のテスト', function () {
    CachedSearchResult::query()->delete(); // キャッシュなしの状態にする

    $response = $this->actingAs($this->user)->get(route('videos.index', [
        'query' => 'Finance'
    ]));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Video/Search')
            ->has('videos', 0)
            ->where('query', 'Finance')
    );
});

test('検索クエリなしのテスト', function () {
    $response = $this->actingAs($this->user)->get('/videos');

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Video/Search')
            ->where('videos', [])
            ->where('query', '')
    );
});

test('検索リクエストの上限超過時のリダイレクト', function () {
    $this->app->bind(VideoService::class, function () {
        return Mockery::mock(VideoService::class, function ($mock) {
            $mock->shouldReceive('searchVideos')
                ->with('coffee')
                ->andThrow(new Exception('Rate limit exceeded', 429));
        });
    });
    
    $response = $this->actingAs($this->user)->get(route('videos.index', [
        'query' => 'coffee'
    ]));

    $response->assertStatus(200)
    ->assertInertia(fn (Assert $page) => 
        $page->component('Errors/QuotaExceeded')
            ->where('message', '検索リクエストの上限に達しました。しばらくしてからお試しください。')
    );
});