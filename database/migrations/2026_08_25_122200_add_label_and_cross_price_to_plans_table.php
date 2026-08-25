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
        Schema::table('plans', function (Blueprint $table) {
            $table->string('label_name')->nullable()->after('name');
            $table->decimal('cross_price', 15, 2)->nullable()->after('price');
        });

        Schema::table('plan_histories', function (Blueprint $table) {
            $table->string('label_name')->nullable()->after('name');
            $table->decimal('cross_price', 15, 2)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['label_name', 'cross_price']);
        });

        Schema::table('plan_histories', function (Blueprint $table) {
            $table->dropColumn(['label_name', 'cross_price']);
        });
    }
};
