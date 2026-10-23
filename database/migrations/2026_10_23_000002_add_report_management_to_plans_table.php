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
                if (!Schema::hasColumn('plans', 'report_management_checkbox')) {
                    $table->string('report_management_checkbox', 1)->default('Y')->after('multi_outlet_checkbox');
                }
            });
        }

        if (Schema::hasTable('plan_histories')) {
            Schema::table('plan_histories', function (Blueprint $table) {
                if (!Schema::hasColumn('plan_histories', 'report_management_checkbox')) {
                    $table->string('report_management_checkbox', 1)->default('Y')->after('multi_outlet_checkbox');
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
                if (Schema::hasColumn('plans', 'report_management_checkbox')) {
                    $table->dropColumn('report_management_checkbox');
                }
            });
        }

        if (Schema::hasTable('plan_histories')) {
            Schema::table('plan_histories', function (Blueprint $table) {
                if (Schema::hasColumn('plan_histories', 'report_management_checkbox')) {
                    $table->dropColumn('report_management_checkbox');
                }
            });
        }
    }
};
