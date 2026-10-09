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
        Schema::create('cash_drawer_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('restaurant_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('transaction_type', 30); // OPENING, CASH_IN, CASH_OUT, ORDER_PAYMENT, SUPPLIER_PAYMENT, MANUAL_ADJUSTMENT
            $table->enum('entry_type', ['CREDIT', 'DEBIT']); // CREDIT = Cash In, DEBIT = Cash Out
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->date('entry_date');
            $table->time('entry_time')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'entry_date']);
            $table->index(['restaurant_id', 'transaction_type']);
            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_drawer_transactions');
    }
};
