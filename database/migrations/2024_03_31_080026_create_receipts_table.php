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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->date('rv_date');
            $table->string('rv_no');
            $table->date('cleared_date');
            $table->string('receipt_to', 255);
            $table->integer('bank_id');
            $table->string('slip_no', 255);
            $table->integer('rv_status')->default(1)->comment('1 = Pending , 2 = Approved');
            $table->integer('voucher_type')->default(1)->comment('1 = Cash , 2 = Bank');
            $table->string('cheque_no');
            $table->date('cheque_date');
            $table->integer('post_dated')->default('1')->comment('1 = Normal , 2 = Post Dated');
            $table->longText('description');
            $table->string('username');
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
            $table->date('date');
            $table->string('time');
            $table->string('approve_username');
            $table->string('delete_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
