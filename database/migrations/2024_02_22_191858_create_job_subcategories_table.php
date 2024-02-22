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
        Schema::create('job_subcategories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_subcategory_id');
            $table->string('job_subcategory_job');
            $table->decimal('job_subcategory_price', 8, 2);
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('job_subcategory_id')->references('id')->on('job_subcategories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_subcategories');
    }
};
