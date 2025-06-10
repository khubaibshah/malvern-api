<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDescriptionColumnOnScsCarsTable extends Migration
{
    public function up(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            // Change description from VARCHAR(255) to TEXT
            $table->text('description')->change();
        });
    }

    public function down(): void
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            // Revert back to VARCHAR(255)
            $table->string('description', 255)->change();
        });
    }
}
