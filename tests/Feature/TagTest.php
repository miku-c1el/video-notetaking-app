<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Tag;
use App\Models\User;
use App\Models\Note;
use App\Models\Video;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('ノートのタグ一覧を取得できる', function () {
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
    $note = Note::factory()->for($this->user)->create([
        'title' => fake()->sentence(),
        'user_id' => $this->user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => 'test_thumbnail.jpg'
    ]);
    $tags = Tag::factory()->count(3)->for($this->user)->create();
    $note->tags()->attach($tags);
    
    $response = $this->getJson(route('tags.index', ['note_id' => $note->id]));
    
    $response->assertStatus(200)
             ->assertJsonCount(3, 'tags');
});

test('タグを名前で検索できる', function () {
    $tag = Tag::factory()->for($this->user)->create(['name' => 'Laravel']);
    Tag::factory()->for($this->user)->create(['name' => 'PHP']);
    
    $response = $this->getJson(route('api.tags.search', ['query' => 'Lar']));
    
    $response->assertStatus(200)
             ->assertJsonCount(1, 'tags')
             ->assertJsonFragment(['name' => 'Laravel']);
});

test('新しいタグを作成してノートに紐付けできる', function () {
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
    $note = Note::factory()->for($this->user)->create([
        'title' => fake()->sentence(),
        'user_id' => $this->user->id,
        'youtubeVideo_id' => $video->youtubeVideo_id,
        'thumbnail' => $video_json->thumbnail
    ]);
    $response = $this->post(route('tags.store'), [
        'name' => 'NewTag',
        'note_id' => $note->id,
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('tags', ['name' => 'NewTag', 'user_id' => $this->user->id]);
    $this->assertDatabaseHas('note_tag', ['note_id' => $note->id]);
});

test('タグの名前を更新できる', function () {
    $tag = Tag::factory()->for($this->user)->create(['name' => 'OldName']);
    
    $response = $this->patch(route('tags.update', $tag), [
        'name' => 'NewName',
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'NewName']);
});

test('タグを削除できる', function () {
    $tag = Tag::factory()->for($this->user)->create();
    
    $response = $this->delete(route('tags.destroy', $tag));
    
    $response->assertRedirect();
    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});


