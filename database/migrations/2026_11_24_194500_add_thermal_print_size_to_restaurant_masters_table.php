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
        if (Schema::hasTable('restaurant_master')) {
            Schema::table('restaurant_master', function (Blueprint $table) {
                if (!Schema::hasColumn('restaurant_master', 'thermal_print_size')) {
                    $table->string('thermal_print_size', 10)->default('80mm')->after('vatin');
                }
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
        if (Schema::hasTable('restaurant_master')) {
            Schema::table('restaurant_master', function (Blueprint $table) {
                if (Schema::hasColumn('restaurant_master', 'thermal_print_size')) {
                    $table->dropColumn('thermal_print_size');
                }
            });
        }
    }
};
