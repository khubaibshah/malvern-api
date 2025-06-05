<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->string('gearbox')->nullable()->after('doors');
            $table->string('keys')->nullable()->after('gearbox');
        });
    }

    public function down(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->dropColumn(['gearbox', 'keys']);
        });
    }
};
