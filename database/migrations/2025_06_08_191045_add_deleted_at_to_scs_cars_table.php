<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->softDeletes(); // This adds a nullable 'deleted_at' TIMESTAMP
        });
    }

    public function down()
    {
        Schema::table('scs_cars', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
