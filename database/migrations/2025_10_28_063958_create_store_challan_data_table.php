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
        Schema::create('store_challan_datas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('location_id');
            $table->integer('material_request_data_id');
            $table->integer('store_challan_id');
            $table->integer('product_variant_id');
            $table->decimal('issue_qty', 15, 3);
            $table->decimal('receive_qty', 15, 3);
            $table->integer('store_challan_status')->default(1);
            $table->integer('status')->default(1);
            $table->date('created_date');
            $table->string('created_by', 150);
            $table->integer('user_id');
            $table->string('approve_username', 150)->nullable();
            $table->date('approve_date')->nullable();
            $table->string('delete_username', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_challan_data');
    }
};
