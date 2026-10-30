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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'sub_category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('sub_category_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('purchase_items') && !Schema::hasColumn('purchase_items', 'sub_category_id')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->unsignedBigInteger('sub_category_id')->nullable()->after('product_id')->index();
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
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'sub_category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('sub_category_id');
            });
        }

        if (Schema::hasTable('purchase_items') && Schema::hasColumn('purchase_items', 'sub_category_id')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropColumn('sub_category_id');
            });
        }
    }
};

