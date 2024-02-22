<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('Booking_reference');
            $table->string('vehicle_make');
            $table->string('vehicle_model');
            $table->unsignedBigInteger('job_repair_id');
            $table->dateTime('bookings_datetime');
            $table->timestamps();
            $table->text('customer_notes')->nullable();
            $table->boolean('deposit_paid')->default(false);
            $table->decimal('repair_price', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_bookings');
    }
};
