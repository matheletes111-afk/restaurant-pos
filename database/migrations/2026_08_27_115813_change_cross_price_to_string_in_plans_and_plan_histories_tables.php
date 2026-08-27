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
        \DB::statement('ALTER TABLE plans MODIFY COLUMN cross_price VARCHAR(255) NULL');
        \DB::statement('ALTER TABLE plan_histories MODIFY COLUMN cross_price VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE plans MODIFY COLUMN cross_price DECIMAL(15, 2) NULL');
        \DB::statement('ALTER TABLE plan_histories MODIFY COLUMN cross_price DECIMAL(15, 2) NULL');
    }
};
