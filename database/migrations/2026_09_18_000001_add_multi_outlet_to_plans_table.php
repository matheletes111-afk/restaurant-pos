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
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'multi_outlet_checkbox')) {
                    $table->string('multi_outlet_checkbox', 1)->default('N')->after('inventory_checkbox');
                }
                if (!Schema::hasColumn('plans', 'total_number_of_outlets')) {
                    $table->integer('total_number_of_outlets')->default(1)->after('multi_outlet_checkbox');
                }
            });
        }

        if (Schema::hasTable('plan_histories')) {
            Schema::table('plan_histories', function (Blueprint $table) {
                if (!Schema::hasColumn('plan_histories', 'multi_outlet_checkbox')) {
                    $table->string('multi_outlet_checkbox', 1)->default('N')->after('inventory_checkbox');
                }
                if (!Schema::hasColumn('plan_histories', 'total_number_of_outlets')) {
                    $table->integer('total_number_of_outlets')->default(1)->after('multi_outlet_checkbox');
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
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (Schema::hasColumn('plans', 'total_number_of_outlets')) {
                    $table->dropColumn('total_number_of_outlets');
                }
                if (Schema::hasColumn('plans', 'multi_outlet_checkbox')) {
                    $table->dropColumn('multi_outlet_checkbox');
                }
            });
        }

        if (Schema::hasTable('plan_histories')) {
            Schema::table('plan_histories', function (Blueprint $table) {
                if (Schema::hasColumn('plan_histories', 'total_number_of_outlets')) {
                    $table->dropColumn('total_number_of_outlets');
                }
                if (Schema::hasColumn('plan_histories', 'multi_outlet_checkbox')) {
                    $table->dropColumn('multi_outlet_checkbox');
                }
            });
        }
    }
};
