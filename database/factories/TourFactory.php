<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->slug(),
            'price_adult' => $this->faker->numberBetween(1000000, 10000000),
            'duration_days' => $this->faker->numberBetween(1, 5),    
            'max_slots' => $this->faker->numberBetween(10, 30),        
            'destination_id' => \App\Models\Destination::factory(),
            'category_id' => \App\Models\TourCategory::factory(),
        ];
    }
}
