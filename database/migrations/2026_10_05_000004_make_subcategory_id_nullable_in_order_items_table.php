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
        if (Schema::hasTable('order_items')) {
            try {
                DB::statement('ALTER TABLE `order_items` MODIFY `subcategory_id` BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                // Ignore if already nullable or driver differences
            }
        }

        if (Schema::hasTable('temp_order_items')) {
            try {
                DB::statement('ALTER TABLE `temp_order_items` MODIFY `subcategory_id` BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                // Ignore if already nullable or driver differences
            }
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
            try {
                DB::statement('ALTER TABLE `order_items` MODIFY `subcategory_id` BIGINT UNSIGNED NOT NULL');
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('temp_order_items')) {
            try {
                DB::statement('ALTER TABLE `temp_order_items` MODIFY `subcategory_id` BIGINT UNSIGNED NOT NULL');
            } catch (\Throwable $e) {}
        }
    }
};
