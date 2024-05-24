<?php

// database/migrations/2024_05_23_212219_create_scs_car_images_table.php
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
        Schema::create('scs_car_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scs_car_id')->constrained()->onDelete('cascade');
            $table->binary('car_image');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scs_car_images');
    }
};
