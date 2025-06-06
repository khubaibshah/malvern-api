<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('source')->nullable(); // e.g. 'website', 'Facebook Ad'
            $table->string('status')->default('new'); // e.g. new, contacted, closed
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('scs_cars')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
