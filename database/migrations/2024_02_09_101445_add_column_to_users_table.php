<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('password'); //Add this column after password column
            $table->string('company_id')->nullable()->after('password');
            $table->string('username')->nullable()->after('company_id');
            $table->string('mobile_no')->nullable()->after('username');
            $table->string('cnic_no')->nullable()->after('mobile_no');
            $table->string('sgpe')->nullable()->after('cnic_no');
            //$table->integer('userEnableDisableStatus')->default(1)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
