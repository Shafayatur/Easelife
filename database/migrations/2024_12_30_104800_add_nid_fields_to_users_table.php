<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNidFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nid_number')->nullable();
            $table->string('nid_document')->nullable();
            $table->enum('nid_verification_status', ['not_verified', 'pending', 'verified', 'rejected'])->default('not_verified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nid_number', 'nid_document', 'nid_verification_status']);
        });
    }
}
