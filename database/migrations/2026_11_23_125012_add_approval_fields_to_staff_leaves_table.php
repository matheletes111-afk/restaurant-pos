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
        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by')->nullable()->after('cancelled_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('admin_remarks')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at', 'admin_remarks']);
        });
    }
};
