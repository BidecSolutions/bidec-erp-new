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
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->integer('ledger_type')->default(1)->comment('1 = Normal Entries (Assets, Expense) (Ammount will be decrease on credit and increase on debit), 2 = Apposite Entries (Income,Liability, Capital) (Ammount will be increase on credit and decrease on debit)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            //
        });
    }
};
