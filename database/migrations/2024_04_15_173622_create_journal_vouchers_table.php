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
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->date('jv_date');
            $table->string('jv_no');
            $table->string('slip_no');
            $table->integer('voucher_type')->default(1)->comment('1 = Normal, 2 = Purchase, 3 = Sale');
            $table->longText('description');
            $table->string('username');
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
            $table->integer('jv_status')->default(1)->comment('1 = Pending , 2 = Approved');
            $table->date('date');
            $table->string('time');
            $table->string('approve_username');
            $table->date('approve_date')->nullable();
            $table->string('approve_time');
            $table->string('delete_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_vouchers');
    }
};
