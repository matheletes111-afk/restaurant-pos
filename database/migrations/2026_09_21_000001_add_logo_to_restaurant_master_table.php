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
        Schema::table('restaurant_master', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_master', 'logo')) {
                $table->string('logo')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurant_master', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_master', 'logo')) {
                $table->dropColumn('logo');
            }
        });
    }
};
