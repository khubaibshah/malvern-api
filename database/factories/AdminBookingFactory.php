<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Get a random user ID from the database
        $user = User::inRandomOrder()->first();

        return [
            'name' => $user->name, // Use the email from the user
            'email' => $user->email, // Use the email from the user
            'phone_number' => $this->faker->phoneNumber,
            'vehicle_make_model' => $this->faker->sentence(3),
            'booking_datetime' => $this->faker->dateTimeBetween('now', '+1 year'),
            'notes' => $this->faker->sentence,
            'user_booking_id' => $user->id, // Associate with the user
        ];
    }
}

