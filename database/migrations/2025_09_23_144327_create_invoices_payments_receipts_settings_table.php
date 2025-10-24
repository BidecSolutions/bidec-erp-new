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
        Schema::create('invoices_payments_receipts_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('type')->comment('1 = Purchase, 2 = Sale');
            $table->integer('option_id')->comment('1 = Purchase Invoice, 2 = Payments, 3 = Sale Invoice, 4 = Receipts');
            $table->integer('acc_id');
            $table->integer('status')->comment('1 = Active, 2 = Inactive');
            $table->string('created_by');
            $table->date('created_date');
            $table->string('updated_by')->nullable();
            $table->date('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_payments_receipts_settings');
    }
};
