<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CachedSearchResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'query' => fake()->unique()->words(3, true),
            'result' => json_encode([
                'videos' => fake()->randomElements([
                    ['id' => fake()->regexify('[A-Za-z0-9_-]{11}'), 'title' => fake()->sentence()],
                    ['id' => fake()->regexify('[A-Za-z0-9_-]{11}'), 'title' => fake()->sentence()],
                ], 2),
            ]),
            'cached_at' => now(),
            'expires_at' => now()->addHours(24),
        ];
    }
}
