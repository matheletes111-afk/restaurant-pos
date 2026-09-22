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
                if (!Schema::hasColumn('restaurant_master', 'parent_id')) {
                    $table->unsignedBigInteger('parent_id')->nullable()->after('id')->index();
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
                if (Schema::hasColumn('restaurant_master', 'parent_id')) {
                    $table->dropColumn('parent_id');
                }
            });
        }
    }
};
