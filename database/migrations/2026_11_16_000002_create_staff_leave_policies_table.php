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
        if (!Schema::hasTable('staff_leave_policies')) {
            Schema::create('staff_leave_policies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('restaurant_id')->nullable()->index();
                $table->integer('year')->default(2026)->index();
                $table->enum('leave_type', ['sick_leave', 'paid_leave', 'early_leave', 'half_day']);
                
                $table->decimal('annual_quota', 5, 2)->default(0.00);
                $table->decimal('carried_forward', 5, 2)->default(0.00);
                $table->decimal('used_count', 5, 2)->default(0.00);
                
                $table->boolean('is_carry_forward')->default(false);
                $table->integer('max_carry_forward')->nullable();
                $table->text('notes')->nullable();
                
                $table->timestamps();

                $table->unique(['user_id', 'leave_type', 'year'], 'staff_leave_unique');
                
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
        Schema::dropIfExists('staff_leave_policies');
    }
};

