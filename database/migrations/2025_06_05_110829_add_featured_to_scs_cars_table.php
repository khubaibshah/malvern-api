<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->boolean('featured')->default(0)->after('price'); // adjust 'after' to position column as needed
        });
    }

    public function down(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->dropColumn('featured');
        });
    }
};