<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEngineSizeToScsCarsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('scs_cars', 'engine_size')) {
            Schema::table('scs_cars', function (Blueprint $table) {
                $table->string('engine_size', 255)->nullable()->after('keys');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('scs_cars', 'engine_size')) {
            Schema::table('scs_cars', function (Blueprint $table) {
                $table->dropColumn('engine_size');
            });
        }
    }
}
