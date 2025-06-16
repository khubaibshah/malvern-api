<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartExToCustomerSaleRequestVehiclesTable extends Migration
{
    public function up()
    {
        Schema::table('customer_sale_request_vehicles', function (Blueprint $table) {
            $table->boolean('part_ex')->default(false)->after('primary_colour');
        });
    }

    public function down()
    {
        Schema::table('customer_sale_request_vehicles', function (Blueprint $table) {
            $table->dropColumn('part_ex');
        });
    }
}
