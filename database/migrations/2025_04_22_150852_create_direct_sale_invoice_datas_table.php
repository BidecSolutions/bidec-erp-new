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
        Schema::create('direct_sale_invoice_datas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('direct_sale_invoice_id');
            $table->integer('product_variant_id');
            $table->integer('qty');
            $table->decimal('rate',15,3);
            $table->decimal('total_amount',15,3);
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
        Schema::dropIfExists('direct_sale_invoice_datas');
    }
};
