<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('make');
            $table->string('colour');
            $table->string('tax_status');
            $table->string('tax_due_date')->nullable();
            $table->string('mot_status');
            $table->string('mot_expiry_date');
            $table->integer('year_of_manufacture');
            $table->string('fuel_type');
            $table->string('type_approval');
            $table->string('date_of_last_v5c_issued')->nullable();
            $table->string('wheelplan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
