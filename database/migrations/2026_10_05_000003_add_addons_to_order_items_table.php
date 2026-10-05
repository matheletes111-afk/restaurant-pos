<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'addons')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->text('addons')->nullable()->after('price');
            });
        }

        if (Schema::hasTable('temp_order_items') && !Schema::hasColumn('temp_order_items', 'addons')) {
            Schema::table('temp_order_items', function (Blueprint $table) {
                $table->text('addons')->nullable()->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'addons')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('addons');
            });
        }

        if (Schema::hasTable('temp_order_items') && Schema::hasColumn('temp_order_items', 'addons')) {
            Schema::table('temp_order_items', function (Blueprint $table) {
                $table->dropColumn('addons');
            });
        }
    }
};
