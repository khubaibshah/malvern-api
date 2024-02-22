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
        Schema::create('jobs_categories', function (Blueprint $table) {
            $table->id();
            $table->string('job_category');
            $table->unsignedBigInteger('job_category_id');
            $table->boolean('active_job')->default(true);
            $table->unsignedBigInteger('job_subcategory_id');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs_categories');
    }
};
