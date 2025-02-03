<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'youtubeVideo_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
            'video_resource' => json_encode([
                'title' => fake()->sentence(),
                'description' => fake()->paragraph(),
                'duration' => fake()->numberBetween(30, 3600),
                'thumbnail' => fake()->imageUrl(),
            ]),
            'cached_at' => now(),
            'expires_at' => now()->addDays(7),
            'status' => 'active',
        ];
    }
}
