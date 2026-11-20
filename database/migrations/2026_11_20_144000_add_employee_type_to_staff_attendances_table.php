<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('staff_attendances') && !Schema::hasColumn('staff_attendances', 'employee_type')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->string('employee_type', 20)->default('permanent')->after('attendance_type')->index();
            });

            // Backfill existing attendances: if user has hourly salary, set employee_type = 'hourly'
            DB::statement("
                UPDATE staff_attendances sa
                INNER JOIN staff_salaries ss ON sa.user_id = ss.user_id
                SET sa.employee_type = 'hourly'
                WHERE ss.employment_type = 'hourly' OR (ss.hourly_wage > 0 AND (ss.monthly_salary IS NULL OR ss.monthly_salary = 0))
            ");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('staff_attendances') && Schema::hasColumn('staff_attendances', 'employee_type')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->dropColumn('employee_type');
            });
        }
    }
};
