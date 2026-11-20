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
        if (!Schema::hasTable('staff_payrolls')) {
            Schema::create('staff_payrolls', function (Blueprint $table) {
                $table->id();
                $table->integer('restaurant_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->integer('year')->index();
                $table->integer('month')->index();

                // Days & Attendance
                $table->integer('total_days')->default(30);
                $table->decimal('present_days', 5, 2)->default(0.00);
                $table->decimal('leave_days', 5, 2)->default(0.00);
                $table->json('leave_breakdown')->nullable();
                $table->decimal('absent_days', 5, 2)->default(0.00);

                // Salary & Deductions
                $table->decimal('base_salary', 12, 2)->default(0.00);
                $table->decimal('absent_deduction', 12, 2)->default(0.00);
                $table->decimal('earned_salary', 12, 2)->default(0.00);

                // Statutory Deductions
                $table->decimal('pf_amount', 10, 2)->default(0.00);
                $table->decimal('esi_amount', 10, 2)->default(0.00);
                $table->decimal('other_deductions', 10, 2)->default(0.00);
                $table->decimal('net_salary', 12, 2)->default(0.00);

                // Extras from staff_extras table
                $table->decimal('extra_earnings', 10, 2)->default(0.00);
                $table->decimal('extra_deductions', 10, 2)->default(0.00);
                $table->decimal('advance_amount', 10, 2)->default(0.00);

                // Final & Actual Payable
                $table->decimal('final_salary', 12, 2)->default(0.00);
                $table->decimal('actual_payable', 12, 2)->default(0.00);

                // Payment Status & Meta
                $table->boolean('is_paid')->default(false);
                $table->date('paid_date')->nullable();
                $table->text('notes')->nullable();

                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->unique(['restaurant_id', 'user_id', 'year', 'month'], 'staff_payroll_month_unique');

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
        Schema::dropIfExists('staff_payrolls');
    }
};
