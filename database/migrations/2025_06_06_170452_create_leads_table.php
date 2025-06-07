<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->text('message')->nullable();
                $table->unsignedBigInteger('vehicle_id')->nullable();
                $table->string('source')->nullable();
                $table->string('status')->default('new');
                $table->timestamps();

                $table->foreign('vehicle_id')->references('id')->on('scs_cars')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
