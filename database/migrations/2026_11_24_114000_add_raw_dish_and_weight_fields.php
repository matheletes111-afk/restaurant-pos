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
        // 1. sub_category table
        if (Schema::hasTable('sub_category') && !Schema::hasColumn('sub_category', 'is_raw_dish')) {
            Schema::table('sub_category', function (Blueprint $table) {
                $table->boolean('is_raw_dish')->default(false)->after('price');
            });
        }

        // 2. order_items table
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'is_raw_dish')) {
                    $table->boolean('is_raw_dish')->default(false)->after('quantity');
                }
                if (!Schema::hasColumn('order_items', 'weight_in_kg')) {
                    $table->decimal('weight_in_kg', 8, 3)->nullable()->after('is_raw_dish');
                }
                if (!Schema::hasColumn('order_items', 'weight_gram')) {
                    $table->integer('weight_gram')->nullable()->after('weight_in_kg');
                }
                if (!Schema::hasColumn('order_items', 'per_kg_price')) {
                    $table->decimal('per_kg_price', 10, 2)->nullable()->after('weight_gram');
                }
            });
        }

        // 3. temp_order_items table
        if (Schema::hasTable('temp_order_items')) {
            Schema::table('temp_order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('temp_order_items', 'is_raw_dish')) {
                    $table->boolean('is_raw_dish')->default(false)->after('quantity');
                }
                if (!Schema::hasColumn('temp_order_items', 'weight_in_kg')) {
                    $table->decimal('weight_in_kg', 8, 3)->nullable()->after('is_raw_dish');
                }
                if (!Schema::hasColumn('temp_order_items', 'weight_gram')) {
                    $table->integer('weight_gram')->nullable()->after('weight_in_kg');
                }
                if (!Schema::hasColumn('temp_order_items', 'per_kg_price')) {
                    $table->decimal('per_kg_price', 10, 2)->nullable()->after('weight_gram');
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
        if (Schema::hasTable('sub_category') && Schema::hasColumn('sub_category', 'is_raw_dish')) {
            Schema::table('sub_category', function (Blueprint $table) {
                $table->dropColumn('is_raw_dish');
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $cols = ['is_raw_dish', 'weight_in_kg', 'weight_gram', 'per_kg_price'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('order_items', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('temp_order_items')) {
            Schema::table('temp_order_items', function (Blueprint $table) {
                $cols = ['is_raw_dish', 'weight_in_kg', 'weight_gram', 'per_kg_price'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('temp_order_items', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
