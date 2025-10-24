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
        Schema::create('balance_sheet_report_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('acc_id');
            $table->integer('acc_type')->default(0)->comment('0 = None, 1 = Assets Section, 2 = Liabilities Section, 3 = Equity Section');
            $table->integer('status')->default('1')->comment('1 = Active, 2 = Inactive');
            $table->string('created_by');
            $table->date('created_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_sheet_report_settings');
    }
};
