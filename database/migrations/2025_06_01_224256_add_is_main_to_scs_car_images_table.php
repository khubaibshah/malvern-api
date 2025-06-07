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
            if (!Schema::hasColumn('scs_car_images', 'is_main')) {
                $table->boolean('is_main')
                      ->default(false)
                      ->after('car_image')
                      ->comment('Flag for primary display image');
            }

            if (!Schema::hasColumn('scs_car_images', 'sort_order')) {
                $table->unsignedInteger('sort_order')
                      ->default(0)
                      ->after('is_main')
                      ->comment('Sorting position of images');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scs_car_images', function (Blueprint $table) {
            if (Schema::hasColumn('scs_car_images', 'is_main')) {
                $table->dropColumn('is_main');
            }

            if (Schema::hasColumn('scs_car_images', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
