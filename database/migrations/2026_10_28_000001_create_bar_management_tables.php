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
        // 1. Bar Brands Table
        if (!Schema::hasTable('bar_brands')) {
            Schema::create('bar_brands', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->string('status', 20)->default('A');
                $table->timestamps();

                $table->index('restaurant_id');
                $table->index('status');
            });
        }

        // 2. Add Bar specific columns to sub_category table
        Schema::table('sub_category', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_category', 'brand_id')) {
                $table->unsignedBigInteger('brand_id')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('sub_category', 'bottle_size_ml')) {
                $table->decimal('bottle_size_ml', 10, 2)->nullable()->default(750.00)->after('price');
            }
            if (!Schema::hasColumn('sub_category', 'opening_qty')) {
                $table->decimal('opening_qty', 10, 2)->nullable()->default(0.00)->after('bottle_size_ml');
            }
            if (!Schema::hasColumn('sub_category', 'current_stock_ml')) {
                $table->decimal('current_stock_ml', 12, 2)->nullable()->default(0.00)->after('opening_qty');
            }
        });

        // 3. Bar Item Variations Table (Pegs, Pours, Sizes)
        if (!Schema::hasTable('bar_item_variations')) {
            Schema::create('bar_item_variations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id');
                $table->unsignedBigInteger('sub_category_id');
                $table->string('name'); // e.g. "30ml Peg", "60ml Large", "750ml Bottle"
                $table->decimal('ml', 10, 2); // Volume in ml (e.g. 30, 60, 750)
                $table->decimal('price', 10, 2); // Selling price in rupees
                $table->string('status', 20)->default('A');
                $table->timestamps();

                $table->index('restaurant_id');
                $table->index('sub_category_id');
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
        Schema::dropIfExists('bar_item_variations');

        Schema::table('sub_category', function (Blueprint $table) {
            if (Schema::hasColumn('sub_category', 'brand_id')) {
                $table->dropColumn('brand_id');
            }
            if (Schema::hasColumn('sub_category', 'bottle_size_ml')) {
                $table->dropColumn('bottle_size_ml');
            }
            if (Schema::hasColumn('sub_category', 'opening_qty')) {
                $table->dropColumn('opening_qty');
            }
            if (Schema::hasColumn('sub_category', 'current_stock_ml')) {
                $table->dropColumn('current_stock_ml');
            }
        });

        Schema::dropIfExists('bar_brands');
    }
};
