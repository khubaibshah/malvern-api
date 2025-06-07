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
        $columns = [
            'vrm',
            'veh_status',
            'man_year',
            'was_price',
            'engine_cc',
            'body_style',
            'stock_id',
            'ebay_gt_title',
            'subtitle',
            'reg_date',
            'trade_price',
            'trade_text',
            'price_above_40k',
        ];

        Schema::table('scs_cars', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('scs_cars', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // You can optionally re-add the columns here
    }
};
