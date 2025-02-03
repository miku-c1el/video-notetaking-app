<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Note;
use App\Models\Moment;

class MomentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Note::all()->each(function ($note) {
            Moment::factory(rand(2, 5))->create([
                'note_id' => $note->id
            ]);
        });
    }
}
