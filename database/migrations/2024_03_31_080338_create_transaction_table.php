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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->integer('acc_id');
            $table->longText('particulars');
            $table->integer('opening_bal')->default(2)->comment('1 = Opening, 2 = Normal');
            $table->integer('debit_credit')->comment('1 = Debit , 2 = Credit');
            $table->decimal('amount', 15, 3);
            $table->integer('voucher_id');
            $table->integer('voucher_type')->comment('1 = Journal Vouchers , 2 = Payments , 3 = Receipts');
            $table->date('v_date');
            $table->date('date');
            $table->string('time');
            $table->string('username');
            $table->string('delete_username');
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
