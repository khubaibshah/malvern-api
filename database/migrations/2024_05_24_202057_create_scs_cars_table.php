<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scs_cars', function (Blueprint $table) {
            $table->id();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('vrm')->nullable();
            $table->date('reg_date')->nullable();
            $table->string('man_year')->nullable();
            $table->string('description')->nullable();
            $table->string('reg_letter')->nullable();
            $table->string('variant')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('plus_vat')->default(false);
            $table->boolean('vat_qualifying')->default(false);
            $table->decimal('was_price', 10, 2)->nullable();
            $table->string('trade_price')->nullable();
            $table->string('trade_text')->nullable();
            $table->boolean('price_above_40k')->default(false);
            $table->integer('mileage')->nullable();
            $table->integer('engine_cc')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('body_style')->nullable();
            $table->string('colour')->nullable();
            $table->integer('doors')->nullable();
            $table->string('veh_type')->nullable();
            $table->string('veh_status')->nullable();
            $table->string('stock_id')->nullable();
            $table->string('ebay_gt_title')->nullable();
            $table->string('subtitle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scs_cars');
    }
};
