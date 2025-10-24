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
        Schema::create('direct_sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->string('dsi_no')->unique();
            $table->date('dsi_date');
            $table->integer('customer_id');
            $table->text('si_note')->nullable();
            $table->text('main_description')->nullable();
            $table->integer('paymentType');
            $table->decimal('payment_type_rate',15,3);
            $table->integer('payment_receipt_status')->default('1')->comment('1 = Pending, 2 = Completed');
            $table->integer('status')->default('1')->comment('1 = Active, 2 = Inactive');
            $table->integer('dsi_status')->default('1')->comment('1 = Pending, 2 = Approve');
            $table->string('created_by');
            $table->date('created_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_sale_invoices');
    }
};
