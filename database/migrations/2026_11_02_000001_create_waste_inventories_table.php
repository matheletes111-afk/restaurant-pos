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
        if (!Schema::hasTable('waste_inventories')) {
            Schema::create('waste_inventories', function (Blueprint $table) {
                $table->id();
                $table->string('waste_no', 100);
                $table->date('waste_date');
                $table->string('item_type', 20)->default('FOOD'); // 'FOOD' or 'BAR'
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('sub_category_id')->nullable();
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->decimal('waste_qty', 12, 2)->default(0.00);
                $table->decimal('current_stock_before', 12, 2)->nullable();
                $table->text('reason')->nullable();
                $table->string('status', 20)->default('COMPLETED');
                $table->unsignedBigInteger('restaurant_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('restaurant_id');
                $table->index('waste_date');
                $table->index('item_type');
                $table->index('product_id');
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
        Schema::dropIfExists('waste_inventories');
    }
};

