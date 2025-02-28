<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Note;
use App\Models\User;
use App\Models\Video;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('ノートを保存できる', function () {
    $user = User::factory()->create();
    $videoData = [
        'title' => 'title Test',
        'videoId' => 'test123',
        'thumbnail' => 'test_thumbnail.jpg'
    ];

    $response = $this
        ->actingAs($user)
        ->post(route('notes.store'), [
            'video' => $videoData,
        ]);

    $this->assertDatabaseHas('notes', [
        'title' => 'title Test',
        'youtubeVideo_id' => 'test123',
        'thumbnail' => 'test_thumbnail.jpg',
        'user_id' => $user->id,
    ]);

    $note = Note::where('title', 'title Test')->firstOrFail();

    $response
        ->assertRedirect(route('notes.show', ['note' => $note->id]));
});

test('ノート一覧を取得できる', function () {
    $videoData = [
        'youtubeVideo_id' => '1',
        'video_resource' => 'json',
    ];

    $video = Video::factory()->create($videoData);
    $user = User::factory()->create();

    Note::factory()->count(5)->create([
        'title' => fake()->sentence(),
        'user_id' => $user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => 'test_thumbnail.jpg'
    ]);

    $response = $this->actingAs($user)->get(route('notes.index'));
    $response->assertStatus(200)
        ->assertInertia(fn ($page) =>
            $page->has('initialNotes.data', 5)
    );
});

test('APIでノート一覧を取得できる', function () {
    $videoData = [
        'youtubeVideo_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
        'video_resource' => json_encode([
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(30, 3600),
            'thumbnail' => fake()->imageUrl(),
        ]),
    ];

    $video = Video::factory()->create($videoData);
    $user = User::factory()->create();
    $video_json = json_decode($video->video_resource);

    Note::factory()->count(5)->create([
        'title' => fake()->sentence(),
        'user_id' => $user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);

    $response = $this->actingAs($user)->get('/api/notes');
    $response->assertStatus(200)
        ->assertJsonCount(5, 'notes');
});

test('特定のノートを取得できる', function () {
    $videoData = [
        'youtubeVideo_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
        'video_resource' => json_encode([
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(30, 3600),
            'thumbnail' => fake()->imageUrl(),
        ]),
    ];

    $video = Video::factory()->create($videoData);
    $video_json = json_decode($video->video_resource);
    $user = User::factory()->create();
    $note = Note::factory()->create([
        'title' => '',
        'user_id' => $user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);

    $response = $this->actingAs($user)->get(route('notes.show', $note->id));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->where('note.id', $note->id)
            ->where('note.title', $note->title)
    );
});

test('自分のノートのタイトルを更新できる', function () {
    $videoData = [
        'youtubeVideo_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
        'video_resource' => json_encode([
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(30, 3600),
            'thumbnail' => fake()->imageUrl(),
        ]),
    ];

    $video = Video::factory()->create($videoData);
    $video_json = json_decode($video->video_resource);
    $user = User::factory()->create();
    $note = Note::factory()->create([
        'title' => '',
        'user_id' => $user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('notes.update', $note->id), [
        'title' => '新しいタイトル'
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => '新しいタイトル'
    ]);
});

test('他ユーザーのノートのタイトルは変更できない', function () {
    $videoData = [
        'youtubeVideo_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
        'video_resource' => json_encode([
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(30, 3600),
            'thumbnail' => fake()->imageUrl(),
        ]),
    ];

    $video = Video::factory()->create($videoData);
    $video_json = json_decode($video->video_resource);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $note = Note::factory()->create([
        'title' => '元のタイトル',
        'user_id' => $user1->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);

    $response = $this
        ->actingAs($user2)
        ->patch(route('notes.update', $note->id), [
        'title' => '新しいタイトル'
    ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => '元のタイトル'
    ]);
});

test('自分のノートを削除できる', function () {
    $user = User::factory()->create();
    $videoData = [
        'youtubeVideo_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
        'video_resource' => json_encode([
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(30, 3600),
            'thumbnail' => fake()->imageUrl(),
        ]),
    ];

    $video = Video::factory()->create($videoData);
    $video_json = json_decode($video->video_resource);
    $note = Note::factory()->create([
        'title' => 'タイトル',
        'user_id' => $user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);

    $response = $this->actingAs($user)->delete(route('notes.destroy', $note->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

test('他ユーザーのノートを削除できない', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $videoData = [
        'youtubeVideo_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
        'video_resource' => json_encode([
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(30, 3600),
            'thumbnail' => fake()->imageUrl(),
        ]),
    ];

    $video = Video::factory()->create($videoData);
    $video_json = json_decode($video->video_resource);
    $note = Note::factory()->create([
        'title' => 'タイトル',
        'user_id' => $user1->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);

    $response = $this->actingAs($user2)->delete(route('notes.destroy', $note->id));

    $response->assertForbidden();
    $this->assertDatabaseHas('notes', [
        'id' => $note->id
    ]);
});