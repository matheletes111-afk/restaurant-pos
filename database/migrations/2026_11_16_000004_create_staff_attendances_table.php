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
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('attendance_date')->index();
            $table->string('attendance_type', 10)->default('P')->comment('P = Full Day Present, H = Half Day, EL = Early Leave');
            $table->string('login_time', 20)->nullable();
            $table->string('logout_time', 20)->nullable()->comment('Optional logout time');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->comment('Admin/User who logged the attendance');
            $table->timestamps();

            // Unique constraint: A staff member can only have ONE attendance record per date in a restaurant
            $table->unique(['restaurant_id', 'user_id', 'attendance_date'], 'unique_staff_daily_attendance');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_attendances');
    }
};

