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
        Schema::table('transaction', function (Blueprint $table) {
            $table->unsignedTinyInteger('voucher_type')
                  ->default(1)
                  ->comment('1 = Journal Vouchers , 2 = Payments , 3 = Receipts, 4 = Sale Journal Vouchers, 5 = Purchase Journal Vouchers, 6 = Generate Fee Voucher Journal Voucher, 7 = Receipt Fees Journal Voucher, 8 = Salaries Journal Voucher')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
        });
    }
};
