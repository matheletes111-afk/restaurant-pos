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
        // Modify order_type to string(50) so it accepts DELIVERY, DINE_IN, TAKEAWAY etc.
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type', 50)->default('DINE_IN')->change();
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('order_type');
            }
            if (!Schema::hasColumn('orders', 'delivery_latitude')) {
                $table->decimal('delivery_latitude', 10, 8)->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('orders', 'delivery_longitude')) {
                $table->decimal('delivery_longitude', 11, 8)->nullable()->after('delivery_latitude');
            }
        });

        // Modify order_type to string(50) in temp_orders table
        Schema::table('temp_orders', function (Blueprint $table) {
            $table->string('order_type', 50)->default('DINE_IN')->change();
            if (!Schema::hasColumn('temp_orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('order_type');
            }
            if (!Schema::hasColumn('temp_orders', 'delivery_latitude')) {
                $table->decimal('delivery_latitude', 10, 8)->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('temp_orders', 'delivery_longitude')) {
                $table->decimal('delivery_longitude', 11, 8)->nullable()->after('delivery_latitude');
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
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_address')) {
                $table->dropColumn(['delivery_address', 'delivery_latitude', 'delivery_longitude']);
            }
        });

        Schema::table('temp_orders', function (Blueprint $table) {
            if (Schema::hasColumn('temp_orders', 'delivery_address')) {
                $table->dropColumn(['delivery_address', 'delivery_latitude', 'delivery_longitude']);
            }
        });
    }
};
