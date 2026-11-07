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
        // 1. Update order_items
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'vat_rate')) {
                    $table->decimal('vat_rate', 5, 2)->default(0.00)->after('gst_rate');
                }
                if (!Schema::hasColumn('order_items', 'vat_amount')) {
                    $table->decimal('vat_amount', 10, 2)->default(0.00)->after('gst_amount');
                }
                if (!Schema::hasColumn('order_items', 'bar_variation_id')) {
                    $table->unsignedBigInteger('bar_variation_id')->nullable()->after('subcategory_id');
                }
                if (!Schema::hasColumn('order_items', 'bar_variation_name')) {
                    $table->string('bar_variation_name', 150)->nullable()->after('bar_variation_id');
                }
                if (!Schema::hasColumn('order_items', 'bar_variation_ml')) {
                    $table->decimal('bar_variation_ml', 10, 2)->nullable()->after('bar_variation_name');
                }
            });
        }

        // 2. Update orders
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'vat_amount')) {
                    $table->decimal('vat_amount', 10, 2)->default(0.00)->after('gst_amount');
                }
            });
        }

        // 3. Update temp_order_items
        if (Schema::hasTable('temp_order_items')) {
            Schema::table('temp_order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('temp_order_items', 'vat_rate')) {
                    $table->decimal('vat_rate', 5, 2)->default(0.00)->after('gst_rate');
                }
                if (!Schema::hasColumn('temp_order_items', 'vat_amount')) {
                    $table->decimal('vat_amount', 10, 2)->default(0.00)->after('gst_amount');
                }
                if (!Schema::hasColumn('temp_order_items', 'bar_variation_id')) {
                    $table->unsignedBigInteger('bar_variation_id')->nullable()->after('subcategory_id');
                }
                if (!Schema::hasColumn('temp_order_items', 'bar_variation_name')) {
                    $table->string('bar_variation_name', 150)->nullable()->after('bar_variation_id');
                }
                if (!Schema::hasColumn('temp_order_items', 'bar_variation_ml')) {
                    $table->decimal('bar_variation_ml', 10, 2)->nullable()->after('bar_variation_name');
                }
            });
        }

        // 4. Update temp_orders
        if (Schema::hasTable('temp_orders')) {
            Schema::table('temp_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('temp_orders', 'vat_amount')) {
                    $table->decimal('vat_amount', 10, 2)->default(0.00)->after('gst_amount');
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
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $columns = ['vat_rate', 'vat_amount', 'bar_variation_id', 'bar_variation_name', 'bar_variation_ml'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('order_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'vat_amount')) {
                    $table->dropColumn('vat_amount');
                }
            });
        }

        if (Schema::hasTable('temp_order_items')) {
            Schema::table('temp_order_items', function (Blueprint $table) {
                $columns = ['vat_rate', 'vat_amount', 'bar_variation_id', 'bar_variation_name', 'bar_variation_ml'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('temp_order_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('temp_orders')) {
            Schema::table('temp_orders', function (Blueprint $table) {
                if (Schema::hasColumn('temp_orders', 'vat_amount')) {
                    $table->dropColumn('vat_amount');
                }
            });
        }
    }
};
