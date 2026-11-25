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
        Schema::table('restaurant_master', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('geofence_radius_meters')->default(50)->after('longitude');
            $table->boolean('geofence_enabled')->default(true)->after('geofence_radius_meters');
        });

        Schema::table('staff_attendances', function (Blueprint $table) {
            $table->decimal('checkin_lat', 10, 8)->nullable()->after('logout_time');
            $table->decimal('checkin_lng', 11, 8)->nullable()->after('checkin_lat');
            $table->decimal('checkin_distance_meters', 8, 2)->nullable()->after('checkin_lng');
            $table->decimal('checkout_lat', 10, 8)->nullable()->after('checkin_distance_meters');
            $table->decimal('checkout_lng', 11, 8)->nullable()->after('checkout_lat');
            $table->decimal('checkout_distance_meters', 8, 2)->nullable()->after('checkout_lng');
            $table->string('geofence_status', 30)->default('verified')->after('checkout_distance_meters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurant_master', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'geofence_radius_meters', 'geofence_enabled']);
        });

        Schema::table('staff_attendances', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_lat',
                'checkin_lng',
                'checkin_distance_meters',
                'checkout_lat',
                'checkout_lng',
                'checkout_distance_meters',
                'geofence_status'
            ]);
        });
    }
};
