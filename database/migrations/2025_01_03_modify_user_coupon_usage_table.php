<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveUserCouponUsageUniqueConstraint extends Migration
{
    public function up()
    {
        // Remove the unique constraint
        DB::statement('ALTER TABLE user_coupon_usage DROP INDEX user_coupon_usage_user_id_coupon_code_unique');
    }

    public function down()
    {
        // Recreate the unique constraint if needed
        Schema::table('user_coupon_usage', function (Blueprint $table) {
            $table->unique(['user_id', 'coupon_code']);
        });
    }
}
