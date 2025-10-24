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
        Schema::create('payable_and_receivable_report_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('option_id')->comment('1 = Payable , 2 = Receivable');
            $table->integer('acc_id');
            $table->integer('status')->comment('1 = Active, 2 = Inactive');
            $table->string('created_by');
            $table->date('created_date');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payable_and_receivable_report_settings');
    }
};
