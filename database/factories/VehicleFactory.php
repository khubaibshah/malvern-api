<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registrationNumber = $this->faker->regexify('[A-Z]{3}[0-9]{3}');

        return [

            'registration_number' => $registrationNumber,
            'make' => $this->faker->randomElement(['audi', 'bmw']),
            'colour' => $this->faker->colorName,
            'tax_status' => $this->faker->randomElement(['Valid', 'Expired']), // Example of random tax status
            'tax_due_date' => $this->faker->date(),
            'mot_status' => $this->faker->randomElement(['Valid', 'Expired']), // Example of random MOT status
            'mot_expiry_date' => $this->faker->date(),
            'year_of_manufacture' => $this->faker->year(),
            'fuel_type' => $this->faker->randomElement(['Petrol', 'Diesel', 'Electric']), // Example of random fuel type
            'type_approval' => $this->faker->word(), // Example of type approval
            'date_of_last_v5c_issued' => $this->faker->date(),
            'wheelplan' => $this->faker->randomElement(['2-Wheel', '3-Wheel', '4-Wheel']), // Example of wheelplan
            
        ];
    }
}
