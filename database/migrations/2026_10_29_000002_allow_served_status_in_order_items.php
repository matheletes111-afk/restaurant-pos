<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        if (Schema::hasTable('order_items')) {
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN `order_status` VARCHAR(30) NULL DEFAULT 'PENDING'");
        }

        if (Schema::hasTable('temp_order_items')) {
            DB::statement("ALTER TABLE `temp_order_items` MODIFY COLUMN `order_status` VARCHAR(30) NULL DEFAULT 'PENDING'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('order_items')) {
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN `order_status` ENUM('PENDING','COOKING','DONE') NULL DEFAULT 'PENDING'");
        }

        if (Schema::hasTable('temp_order_items')) {
            DB::statement("ALTER TABLE `temp_order_items` MODIFY COLUMN `order_status` ENUM('PENDING','COOKING','DONE') NULL DEFAULT 'PENDING'");
        }
    }
};
