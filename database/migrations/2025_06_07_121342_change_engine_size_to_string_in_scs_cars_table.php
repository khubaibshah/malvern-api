<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->string('engine_size', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->integer('engine_size')->nullable()->change();
        });
    }
};
