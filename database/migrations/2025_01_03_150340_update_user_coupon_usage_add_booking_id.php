<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserCouponUsageAddBookingIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_coupon_usage', function (Blueprint $table) {
            if (!Schema::hasColumn('user_coupon_usage', 'booking_id')) {
                $table->unsignedBigInteger('booking_id')->nullable();
                
                $table->foreign('booking_id')
                      ->references('id')
                      ->on('bookings')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_coupon_usage', function (Blueprint $table) {
            if (Schema::hasColumn('user_coupon_usage', 'booking_id')) {
                $table->dropForeign(['booking_id']);
                $table->dropColumn('booking_id');
            }
        });
    }
}
