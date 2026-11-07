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
        Schema::table('bar_item_variations', function (Blueprint $table) {
            if (!Schema::hasColumn('bar_item_variations', 'deleted_at')) {
                $table->softDeletes();
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
        Schema::table('bar_item_variations', function (Blueprint $table) {
            if (Schema::hasColumn('bar_item_variations', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
