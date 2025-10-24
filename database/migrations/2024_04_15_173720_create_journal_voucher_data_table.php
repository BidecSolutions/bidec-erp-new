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
        Schema::create('journal_voucher_data', function (Blueprint $table) {
            $table->id();
            $table->integer('journal_voucher_id');
            $table->integer('acc_id');
            $table->text('description');
            $table->integer('debit_credit')->comment('1 = Debit , 2 = Credit');
            $table->decimal('amount', 15, 3)->default(0);
            $table->integer('jv_status')->default(1)->comment('1 = Pending , 2 = Approve');
            $table->string('time');
            $table->date('date');
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
            $table->string('username');
            $table->string('approve_username');
            $table->string('delete_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_voucher_data');
    }
};
