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
        Schema::table('journal_vouchers', function (Blueprint $table) {
            $table->unsignedTinyInteger('voucher_type')
                  ->default(1)
                  ->comment('1=Normal, 2=Purchase, 3=Sale, 4 = Generate Fee Journal Voucher, 5 = Receipt Fee Journal Voucher, 6 = Generate Salary Journal Voucher')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_vouchers', function (Blueprint $table) {
            //
        });
    }
};
