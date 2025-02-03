<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Video;
use App\Models\Tag;
use App\Models\Note;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            $videos = Video::inRandomOrder()->limit(3)->get();
            
            foreach ($videos as $video) {
                Note::factory()->create([
                    'user_id' => $user->id,
                    'youtubeVideo_id' => $video->youtubeVideo_id
                ])->each(function ($note) {
                    // Attach random tags to each note
                    $tags = Tag::where('user_id', $note->user_id)
                              ->inRandomOrder()
                              ->limit(3)
                              ->get();
                    $note->tags()->attach($tags->pluck('id'));
                });
            }
        });
    }
}
