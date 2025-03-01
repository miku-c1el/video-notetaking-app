<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Moment;
use App\Models\Note;
use App\Models\User;
use App\Models\Video;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

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
    $this->note = Note::factory()->create([
        'title' => fake()->sentence(),
        'user_id' => $this->user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);
    $this->actingAs($this->user);
});

test('Momentを作成できる', function () {
    $response = $this->post(route('moments.store'), [
        'noteId' => $this->note->id,
        'title' => 'New Moment',
        'content' => 'Moment content',
        'timestamp' => 1000,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('moment.title', 'New Moment')
        ->assertJsonPath('moment.content', 'Moment content');
});

test('Momentの一覧を取得できる', function () {
    Moment::factory()->count(3)->create(['note_id' => $this->note->id]);

    $response = $this->getJson(route('moments.index', ['note_id' => $this->note->id]));

    $response->assertStatus(200)
        ->assertJsonCount(3, 'moments');
});

test('Momentを更新できる', function () {
    $moment = Moment::factory()->create(['note_id' => $this->note->id]);

    $response = $this->putJson(route('moments.update', $moment->id), [
        'title' => 'Updated Title',
        'content' => 'Updated Content',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('moment.title', 'Updated Title')
        ->assertJsonPath('moment.content', 'Updated Content');
});

test('Momentを削除できる', function () {
    $moment = Moment::factory()->create(['note_id' => $this->note->id]);

    $response = $this->deleteJson(route('moments.destroy', $moment->id));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('moments', ['id' => $moment->id]);
});