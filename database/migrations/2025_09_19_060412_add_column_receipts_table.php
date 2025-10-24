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
        Schema::table('receipts', function (Blueprint $table) {
            $table->integer('si_id')->default(0)->after('dsi_id');
            $table->integer('rv_type')->default(1)->comment('1 = Normal Receipt Voucher, 2 = Sale Receipt Vouchers, 3 = Sale Invoice')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('si_id');
            $table->integer('rv_type')->default(1)->comment('1 = Normal Receipt Voucher, 2 = Sale Receipt Vouchers')->change();
        });
    }
};
