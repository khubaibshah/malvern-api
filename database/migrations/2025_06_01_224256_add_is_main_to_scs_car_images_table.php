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
        Schema::table('scs_car_images', function (Blueprint $table) {
            $table->boolean('is_main')
                  ->default(false)
                  ->after('car_image')
                  ->comment('Flag for primary display image');
            
            $table->unsignedInteger('sort_order')
                  ->default(0)
                  ->after('is_main')
                  ->comment('Sorting position of images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scs_car_images', function (Blueprint $table) {
            $table->dropColumn(['is_main', 'sort_order']);
        });
    }
};