<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFieldsToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('bookings', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'additional_description')) {
                $table->text('additional_description')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            
            // Update status enum to include 'accepted'
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'accepted', 'completed', 'rejected') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city',
                'postal_code',
                'additional_description',
                'rejection_reason'
            ]);
            
            // Revert status enum
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('new', 'pending', 'completed', 'rejected') DEFAULT 'new'");
        });
    }
}
