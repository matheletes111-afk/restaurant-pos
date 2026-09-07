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
        if (!Schema::hasColumn('restaurant_master', 'restaurant_id_unique')) {
            Schema::table('restaurant_master', function (Blueprint $table) {
                $table->string('restaurant_id_unique', 50)->nullable()->unique()->after('id');
            });
        }

        // Find the maximum existing restaurant_id_unique number
        $existing = DB::table('restaurant_master')
            ->whereNotNull('restaurant_id_unique')
            ->pluck('restaurant_id_unique');

        $maxNum = 0;
        foreach ($existing as $val) {
            if (preg_match('/BILL-BITE-(\d+)/i', $val, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        // Populate existing restaurants in order of id
        $restaurants = DB::table('restaurant_master')
            ->whereNull('restaurant_id_unique')
            ->orWhere('restaurant_id_unique', '')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($restaurants as $rest) {
            $maxNum++;
            $code = 'BILL-BITE-' . str_pad($maxNum, 3, '0', STR_PAD_LEFT);
            DB::table('restaurant_master')->where('id', $rest->id)->update([
                'restaurant_id_unique' => $code
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('restaurant_master', 'restaurant_id_unique')) {
            Schema::table('restaurant_master', function (Blueprint $table) {
                $table->dropColumn('restaurant_id_unique');
            });
        }
    }
};
