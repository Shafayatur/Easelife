<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestructureUserCouponUsageTable extends Migration
{
    public function up()
    {
        // Drop the existing table
        Schema::dropIfExists('user_coupon_usage');

        // Create a new table without the unique constraint
        Schema::create('user_coupon_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('coupon_code');
            $table->unsignedBigInteger('booking_id');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_coupon_usage');

        // Recreate the original table if needed
        Schema::create('user_coupon_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('coupon_code');
            $table->unsignedBigInteger('booking_id');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            $table->unique(['user_id', 'coupon_code']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }
}
