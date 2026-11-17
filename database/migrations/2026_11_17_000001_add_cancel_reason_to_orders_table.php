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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('orders', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancel_reason');
            }
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE orders MODIFY COLUMN order_status VARCHAR(50) DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
            if (Schema::hasColumn('orders', 'cancelled_by')) {
                $table->dropColumn('cancelled_by');
            }
        });
    }
};
