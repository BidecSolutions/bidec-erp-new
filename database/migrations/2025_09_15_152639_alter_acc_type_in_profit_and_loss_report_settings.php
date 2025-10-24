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
        Schema::table('profit_and_loss_report_settings', function (Blueprint $table) {
            $table->integer('acc_type')
                ->default(0)
                ->comment('0 = None, 1 = Revenue Section, 2 = Expense Section, 3 = COGS, 4 = Sales')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profit_and_loss_report_settings', function (Blueprint $table) {
            $table->integer('acc_type')->nullable()->change();
        });
    }
};
