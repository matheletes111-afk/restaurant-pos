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
        // Alter food_type from ENUM('VEG', 'NON-VEG') to VARCHAR(30) to support 'BAR', 'VEG', 'NON-VEG'
        DB::statement("ALTER TABLE `sub_category` MODIFY `food_type` VARCHAR(30) NULL DEFAULT 'VEG'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `sub_category` MODIFY `food_type` ENUM('VEG', 'NON-VEG') NULL DEFAULT 'VEG'");
    }
};
