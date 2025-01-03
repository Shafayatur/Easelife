<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUserCouponUsageTable extends Migration
{
    public function up()
    {
        Schema::table('user_coupon_usage', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('user_coupon_usage', 'current_usage')) {
                $table->integer('current_usage')->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('user_coupon_usage', 'max_usage')) {
                $table->integer('max_usage')->default(2)->after('current_usage');
            }
        });

        // Update existing records to have default values
        DB::statement('UPDATE user_coupon_usage SET current_usage = 1, max_usage = 2 WHERE current_usage IS NULL');
    }

    public function down()
    {
        Schema::table('user_coupon_usage', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('user_coupon_usage', 'current_usage')) {
                $table->dropColumn('current_usage');
            }
            if (Schema::hasColumn('user_coupon_usage', 'max_usage')) {
                $table->dropColumn('max_usage');
            }
        });
    }
}
