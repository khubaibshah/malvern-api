<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customer_sale_request_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('primary_colour')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('engine_size')->nullable();
            $table->string('odometer_value')->nullable();
            $table->string('odometer_unit')->nullable();
            $table->date('first_used_date')->nullable();
            $table->date('registration_date')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sale_request_vehicles');
    }
};
