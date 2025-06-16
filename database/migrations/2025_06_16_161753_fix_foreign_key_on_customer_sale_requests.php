<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_sale_requests', function (Blueprint $table) {
            // Drop incorrect foreign key first
            $table->dropForeign(['vehicle_id']);

            // Add correct foreign key
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('customer_sale_request_vehicles')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('customer_sale_requests', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);

            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onDelete('cascade');
        });
    }
};
