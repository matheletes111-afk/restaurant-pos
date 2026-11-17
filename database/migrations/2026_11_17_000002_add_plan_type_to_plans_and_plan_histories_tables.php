<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('plans', 'plan_type')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->string('plan_type', 20)->default('DOMESTIC')->after('name');
            });
        }

        if (!Schema::hasColumn('plan_histories', 'plan_type')) {
            Schema::table('plan_histories', function (Blueprint $table) {
                $table->string('plan_type', 20)->default('DOMESTIC')->after('name');
            });
        }

        // Set all existing plans and plan histories to DOMESTIC
        DB::table('plans')->whereNull('plan_type')->orWhere('plan_type', '')->update(['plan_type' => 'DOMESTIC']);
        DB::table('plan_histories')->whereNull('plan_type')->orWhere('plan_type', '')->update(['plan_type' => 'DOMESTIC']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('plans', 'plan_type')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('plan_type');
            });
        }

        if (Schema::hasColumn('plan_histories', 'plan_type')) {
            Schema::table('plan_histories', function (Blueprint $table) {
                $table->dropColumn('plan_type');
            });
        }
    }
};
