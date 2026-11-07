<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('order_items', 'is_bar_stock_deducted')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->tinyInteger('is_bar_stock_deducted')->default(0)->after('order_status');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('order_items', 'is_bar_stock_deducted')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('is_bar_stock_deducted');
            });
        }
    }
};

