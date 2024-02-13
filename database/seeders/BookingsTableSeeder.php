<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;

class BookingsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Booking::factory()->count(250)->create();
    }
    // /**
    //  * Run the database seeds.
    //  *
    //  * @return void
    //  */
    // public function run()
    // {
    //     Booking::create([
    //         'name' => 'John Doe',
    //         'email' => 'john@example.com',
    //         'phone_number' => '1234567890',
    //         'vehicle_make_model' => 'Toyota Camry',
    //         'booking_datetime' => now(),
    //         'notes' => 'Example booking',
    //     ]);

    // }
}
