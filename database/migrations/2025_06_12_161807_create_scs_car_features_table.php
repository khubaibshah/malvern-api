<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scs_car_features', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('scs_car_id');
            $table->foreign('scs_car_id')->references('id')->on('scs_cars')->onDelete('cascade');

            $table->string('service_history')->nullable();

            $table->date('last_mot')->nullable()->index(); // Indexed
            $table->decimal('tax_cost', 8, 2)->nullable();

            $table->enum('log_book', ['none', 'partial', 'full'])->nullable()->index(); // Indexed
            $table->integer('previous_owners')->nullable()->index(); // Indexed

            $table->enum('interior_condition', ['poor', 'average', 'good', 'excellent'])->nullable()->index(); // Indexed
            $table->enum('exterior_condition', ['poor', 'average', 'good', 'excellent'])->nullable()->index(); // Indexed
            $table->enum('tyre_condition', ['poor', 'average', 'good', 'excellent'])->nullable()->index(); // Indexed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scs_car_features');
    }
};
