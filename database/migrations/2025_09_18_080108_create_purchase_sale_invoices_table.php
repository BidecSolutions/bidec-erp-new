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
        Schema::create('purchase_sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('invoice_type')->comment('1 = Purchase Invoice, 2 = Sale Invoice');
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->string('slip_no')->nullable();
            $table->integer('jv_id');
            $table->integer('supplier_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('debit_account_id')->nullable();
            $table->integer('credit_account_id')->nullable();
            $table->decimal('amount',15,3);
            $table->string('description');
            $table->integer('voucher_status')->comment('1 = Pending, 2 = Approved');
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
        Schema::dropIfExists('purchase_sale_invoices');
    }
};
