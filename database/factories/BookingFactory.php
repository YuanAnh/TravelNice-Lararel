<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_code' => 'TN' . $this->faker->unique()->numerify('######'),
            'total_price' => $this->faker->numberBetween(5000000, 20000000),
            'discount_amount' => 0,
            'status' => 'pending',
            'user_id' => \App\Models\User::factory(),
            'tour_slot_id' => \App\Models\TourSlot::factory(),
        ];
    }
}
