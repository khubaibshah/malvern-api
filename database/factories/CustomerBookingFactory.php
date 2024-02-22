<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerBooking>
 */
class CustomerBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Booking_reference' => $this->faker->randomNumber(2),
            'vehicle_make' => $this->faker->randomElement(['Toyota', 'Honda', 'Ford']),
            'vehicle_model' => $this->faker->word,
            'job_repair_id' => $this->faker->randomNumber(1),
            'bookings_datetime' => $this->faker->dateTime(),
            'customer_notes' => $this->faker->sentence,
            'deposit_paid' => $this->faker->boolean,
            'repair_price' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
