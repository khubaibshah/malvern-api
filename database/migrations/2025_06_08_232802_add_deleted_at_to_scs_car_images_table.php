<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToScsCarImagesTable extends Migration
{
    public function up()
    {
        Schema::table('scs_car_images', function (Blueprint $table) {
            $table->softDeletes(); // adds 'deleted_at' column
        });
    }

    public function down()
    {
        Schema::table('scs_car_images', function (Blueprint $table) {
            $table->dropSoftDeletes(); // drops 'deleted_at'
        });
    }
}