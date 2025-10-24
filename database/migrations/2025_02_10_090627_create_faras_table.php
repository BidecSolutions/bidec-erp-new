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
        Schema::create('faras', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('to_company_id')->nullable();
            $table->integer('to_company_location_id')->nullable();
            $table->integer('process_type')->default(0)->comment('1 = Normal, 2 = Direct Good Receipt Note');
            $table->integer('status')->default(0)->comment('1 = Sale, 2 = Purchase, 3 = Transfer Note');
            $table->integer('customer_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('main_table_id');
            $table->integer('main_table_data_id');
            $table->string('transfer_note_no')->nullable();
            $table->date('transfer_note_date')->nullable();
            $table->string('order_no')->nullable();
            $table->date('order_date')->nullable();
            $table->string('po_no')->nullable();
            $table->date('po_date')->nullable();
            $table->string('grn_no')->nullable();
            $table->date('grn_date')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('product_variant_id')->nullable();
            $table->decimal('qty',15,3)->nullable();
            $table->decimal('rate',15,3)->nullable();
            $table->decimal('amount',15,3)->nullable();
            $table->text('remarks')->nullable();
            $table->string('created_by');
            $table->date('created_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faras');
    }
};
