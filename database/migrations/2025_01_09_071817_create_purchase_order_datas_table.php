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
        Schema::create('purchase_order_datas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('purchase_order_id');
            $table->integer('product_variant_id');
            $table->decimal('qty')->default(0);
            $table->decimal('unit_price')->default(0);
            $table->decimal('sub_total')->default(0);
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
        Schema::dropIfExists('purchase_order_datas');
    }
};
