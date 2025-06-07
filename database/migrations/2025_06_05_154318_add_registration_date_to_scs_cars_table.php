<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('scs_cars', 'registration_date')) {
                $table->date('registration_date')->nullable()->after('registration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            if (Schema::hasColumn('scs_cars', 'registration_date')) {
                $table->dropColumn('registration_date');
            }
        });
    }
};
