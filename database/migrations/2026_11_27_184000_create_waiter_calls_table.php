<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('waiter_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('table_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->boolean('is_attended')->default(false)->index();
            $table->timestamp('attended_at')->nullable();
            $table->unsignedBigInteger('attended_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiter_calls');
    }
};
