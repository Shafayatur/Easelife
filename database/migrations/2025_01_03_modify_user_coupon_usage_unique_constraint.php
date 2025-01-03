<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyUserCouponUsageUniqueConstraint extends Migration
{
    public function up()
    {
        // Drop the existing unique constraint
        DB::statement('ALTER TABLE user_coupon_usage DROP INDEX user_coupon_usage_user_id_coupon_code_unique');
        
        // Add a new unique constraint that includes booking_id
        DB::statement('ALTER TABLE user_coupon_usage ADD UNIQUE INDEX unique_user_coupon_booking (user_id, coupon_code, booking_id)');
    }

    public function down()
    {
        // Revert the changes
        DB::statement('ALTER TABLE user_coupon_usage DROP INDEX unique_user_coupon_booking');
        DB::statement('ALTER TABLE user_coupon_usage ADD UNIQUE INDEX user_coupon_usage_user_id_coupon_code_unique (user_id, coupon_code)');
    }
}
