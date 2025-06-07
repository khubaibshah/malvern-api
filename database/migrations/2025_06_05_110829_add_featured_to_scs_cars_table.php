<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('scs_cars', 'featured')) {
                $table->boolean('featured')
                    ->default(0)
                    ->after('price'); // Adjust position as needed
            }
        });
    }

    public function down(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            if (Schema::hasColumn('scs_cars', 'featured')) {
                $table->dropColumn('featured');
            }
        });
    }
};
