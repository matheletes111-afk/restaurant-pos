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
        Schema::create('demo_leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('restaurant_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email_address');
            $table->string('source')->nullable(); // How did you hear about us?
            $table->string('status')->default('Contacted'); // Contacted, Qualified, Nurturing
            $table->dateTime('followup_date')->nullable();
            $table->text('followup_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demo_leads');
    }
};
