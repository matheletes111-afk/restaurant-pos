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
            $table->string('qr_code_image')->nullable()->after('fssai_number');
            $table->string('upi_id')->nullable()->after('qr_code_image');
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
            $table->dropColumn(['qr_code_image', 'upi_id']);
        });
    }
};
