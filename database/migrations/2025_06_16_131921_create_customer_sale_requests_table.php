<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('customer_sale_requests', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->string('email');
        $table->string('postcode');
        $table->string('phone')->nullable();
        $table->unsignedBigInteger('vehicle_id')->nullable();
        $table->timestamps();

        $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sale_requests');
    }
};
