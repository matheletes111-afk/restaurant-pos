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
        // 1. Add vatin and vat_percentage to restaurant_master
        Schema::table('restaurant_master', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_master', 'vatin')) {
                $table->string('vatin', 50)->nullable()->after('gstin');
            }
            if (!Schema::hasColumn('restaurant_master', 'vat_percentage')) {
                $table->decimal('vat_percentage', 5, 2)->nullable()->default(0.00)->after('gst_percentage');
            }
        });

        // 2. Add vat_rate to sub_category table
        Schema::table('sub_category', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_category', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->nullable()->default(0.00)->after('gst_rate');
            }
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
            if (Schema::hasColumn('restaurant_master', 'vatin')) {
                $table->dropColumn('vatin');
            }
            if (Schema::hasColumn('restaurant_master', 'vat_percentage')) {
                $table->dropColumn('vat_percentage');
            }
        });

        Schema::table('sub_category', function (Blueprint $table) {
            if (Schema::hasColumn('sub_category', 'vat_rate')) {
                $table->dropColumn('vat_rate');
            }
        });
    }
};
