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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->date('po_date')->nullable();
            // $table->string('delivery_place')->nullable();
            $table->string('invoice_quotation_no')->nullable();
            $table->date('quotation_date')->nullable();
            $table->text('main_description')->nullable();
            $table->integer('paymentType')->nullable();
            $table->decimal('payment_type_rate')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->text('po_note')->nullable();
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
            $table->string('created_by');
            $table->date('created_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
