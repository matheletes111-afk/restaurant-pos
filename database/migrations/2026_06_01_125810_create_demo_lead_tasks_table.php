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
        Schema::create('demo_lead_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demo_lead_id');
            $table->string('task_title');
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('demo_lead_id')->references('id')->on('demo_leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demo_lead_tasks');
    }
};
