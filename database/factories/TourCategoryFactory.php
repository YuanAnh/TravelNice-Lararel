<?php

namespace Database\Factories;

use App\Models\TourCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourCategory>
 */
class TourCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
        ];
    }
}
