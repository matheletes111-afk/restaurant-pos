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
        if (!Schema::hasTable('staff_salary_payments')) {
            Schema::create('staff_salary_payments', function (Blueprint $table) {
                $table->id();
                $table->integer('restaurant_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->date('payment_date')->index();
                $table->decimal('amount', 10, 2);
                $table->string('payment_method', 20)->default('CASH'); // CASH or ONLINE
                $table->date('period_from')->nullable()->index();
                $table->date('period_to')->nullable()->index();
                $table->string('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('staff_salary_payments');
    }
};
