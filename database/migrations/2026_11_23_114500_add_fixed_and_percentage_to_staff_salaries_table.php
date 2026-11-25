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
        if (Schema::hasTable('staff_salaries')) {
            Schema::table('staff_salaries', function (Blueprint $table) {
                if (!Schema::hasColumn('staff_salaries', 'ptax_type')) {
                    $table->string('ptax_type', 20)->default('percentage')->after('esi_percentage');
                }
                if (!Schema::hasColumn('staff_salaries', 'ptax_amount')) {
                    $table->decimal('ptax_amount', 12, 2)->default(0.00)->after('ptax_percentage');
                }
                if (!Schema::hasColumn('staff_salaries', 'other_deduction_type')) {
                    $table->string('other_deduction_type', 20)->default('percentage')->after('ptax_amount');
                }
                if (!Schema::hasColumn('staff_salaries', 'other_deduction_amount')) {
                    $table->decimal('other_deduction_amount', 12, 2)->default(0.00)->after('other_deduction_percentage');
                }
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
        if (Schema::hasTable('staff_salaries')) {
            Schema::table('staff_salaries', function (Blueprint $table) {
                $columns = ['ptax_type', 'ptax_amount', 'other_deduction_type', 'other_deduction_amount'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('staff_salaries', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
