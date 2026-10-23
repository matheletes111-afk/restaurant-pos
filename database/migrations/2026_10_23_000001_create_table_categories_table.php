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
        if (!Schema::hasTable('table_categories')) {
            Schema::create('table_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('status', 10)->default('A'); // A = Active, I = Inactive, D = Deleted
                $table->timestamps();
            });
        }

        if (Schema::hasTable('table_management')) {
            if (!Schema::hasColumn('table_management', 'table_category_id')) {
                Schema::table('table_management', function (Blueprint $table) {
                    $table->unsignedBigInteger('table_category_id')->nullable()->after('description')->index();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('table_management') && Schema::hasColumn('table_management', 'table_category_id')) {
            Schema::table('table_management', function (Blueprint $table) {
                $table->dropColumn('table_category_id');
            });
        }

        Schema::dropIfExists('table_categories');
    }
};
