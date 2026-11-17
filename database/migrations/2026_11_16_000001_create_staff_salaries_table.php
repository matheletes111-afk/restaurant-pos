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
        if (!Schema::hasTable('staff_salaries')) {
            Schema::create('staff_salaries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->integer('restaurant_id')->nullable()->index();
                $table->enum('employment_type', ['permanent', 'hourly'])->default('permanent');
                
                // Permanent structure
                $table->decimal('monthly_salary', 12, 2)->nullable()->default(0.00);
                $table->decimal('pf_percentage', 6, 2)->nullable()->default(0.00);
                $table->decimal('esi_percentage', 6, 2)->nullable()->default(0.00);
                $table->decimal('ptax_percentage', 6, 2)->nullable()->default(0.00);
                $table->decimal('other_deduction_percentage', 6, 2)->nullable()->default(0.00);
                $table->integer('weekoff_count')->nullable()->default(4);

                // Hourly structure
                $table->decimal('hourly_wage', 12, 2)->nullable()->default(0.00);

                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
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
        Schema::dropIfExists('staff_salaries');
    }
};

