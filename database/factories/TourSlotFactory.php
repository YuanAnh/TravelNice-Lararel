<?php

namespace Database\Factories;

use App\Models\TourSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourSlot>
 */
class TourSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id' => \App\Models\Tour::factory(),
            'departure_date' => now()->addDays(10), 
            'total_slots' => 20,
        ];
    }
}
