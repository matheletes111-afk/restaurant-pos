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
        if (!Schema::hasTable('dish_addon_mappings')) {
            Schema::create('dish_addon_mappings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->nullable()->index();
                $table->unsignedBigInteger('sub_category_id')->index();
                $table->unsignedBigInteger('dish_addon_id')->index();
                $table->timestamps();

                $table->unique(['sub_category_id', 'dish_addon_id'], 'dish_addon_unique_mapping');
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
        Schema::dropIfExists('dish_addon_mappings');
    }
};
