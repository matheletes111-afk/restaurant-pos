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
        // 1. Orders table: Ensure discount columns exist
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0.00)->after('taxable_amount');
            }
            if (!Schema::hasColumn('orders', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('discount');
            }
            if (!Schema::hasColumn('orders', 'discount_type')) {
                $table->string('discount_type', 20)->default('FIXED')->after('discount_percentage');
            }
        });

        // 2. Temp Orders table: Ensure discount columns exist
        if (Schema::hasTable('temp_orders')) {
            Schema::table('temp_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('temp_orders', 'discount')) {
                    $table->decimal('discount', 12, 2)->default(0.00)->after('taxable_amount');
                }
                if (!Schema::hasColumn('temp_orders', 'discount_percentage')) {
                    $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('discount');
                }
                if (!Schema::hasColumn('temp_orders', 'discount_type')) {
                    $table->string('discount_type', 20)->default('FIXED')->after('discount_percentage');
                }
            });
        }

        // 3. Order Items table: Ensure item-level discount columns exist
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'discounted_price')) {
                    $table->decimal('discounted_price', 10, 2)->default(0.00)->after('price');
                }
                if (!Schema::hasColumn('order_items', 'item_discount_percentage')) {
                    $table->decimal('item_discount_percentage', 5, 2)->default(0.00)->after('discounted_price');
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
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
        });

        if (Schema::hasTable('temp_orders')) {
            Schema::table('temp_orders', function (Blueprint $table) {
                if (Schema::hasColumn('temp_orders', 'discount_type')) {
                    $table->dropColumn('discount_type');
                }
            });
        }
    }
};
