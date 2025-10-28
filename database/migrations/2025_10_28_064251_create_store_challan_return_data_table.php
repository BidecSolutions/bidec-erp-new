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
        Schema::create('store_challan_return_data', function (Blueprint $table) {
            $table->id();
            $table->integer('accounting_year');
            $table->string('store_challan_return_no', 150);
            $table->date('store_challan_return_date');
            $table->string('store_challan_no', 150);
            $table->date('store_challan_date');
            $table->integer('store_challan_data_id')->default(0);
            $table->integer('category_id');
            $table->integer('sub_item_id');
            $table->decimal('return_qty', 15, 3);
            $table->integer('store_challan_return_status')->default(1);
            $table->integer('status')->default(1);
            $table->date('date');
            $table->string('time', 20);
            $table->string('username', 150);
            $table->string('approve_username', 150);
            $table->string('delete_username', 150);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_challan_return_data');
    }
};
