<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('scs_cars', 'gearbox')) {
                $table->string('gearbox')->nullable()->after('doors');
            }

            if (!Schema::hasColumn('scs_cars', 'keys')) {
                $table->string('keys')->nullable()->after('gearbox');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            if (Schema::hasColumn('scs_cars', 'gearbox')) {
                $table->dropColumn('gearbox');
            }

            if (Schema::hasColumn('scs_cars', 'keys')) {
                $table->dropColumn('keys');
            }
        });
    }
};
