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
        if (!Schema::hasColumn('category', 'category_type')) {
            Schema::table('category', function (Blueprint $table) {
                $table->string('category_type', 20)->default('RES')->after('name');
            });

            // Ensure all existing categories are defaulted to 'RES'
            DB::table('category')
                ->whereNull('category_type')
                ->orWhere('category_type', '')
                ->update(['category_type' => 'RES']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('category', 'category_type')) {
            Schema::table('category', function (Blueprint $table) {
                $table->dropColumn('category_type');
            });
        }
    }
};
