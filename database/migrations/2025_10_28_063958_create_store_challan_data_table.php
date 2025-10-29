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
        Schema::create('store_challan_data', function (Blueprint $table) {
            $table->id();
            $table->integer('accounting_year');
            $table->integer('company_id');
            $table->integer('material_request_data_id');
            $table->string('store_challan_no', 150);
            $table->date('store_challan_date');
            $table->integer('category_id');
            $table->integer('sub_item_id');
            $table->decimal('issue_qty', 15, 3);
            $table->decimal('receive_qty', 15, 3);
            $table->text('sub_description');
            $table->integer('store_challan_status')->default(1);
            $table->integer('status')->default(1);
            $table->date('date');
            $table->string('time', 20);
            $table->string('username', 150);
            $table->integer('user_id');
            $table->string('approve_username', 150);
            $table->date('approve_date');
            $table->string('approve_time', 20);
            $table->string('delete_username', 150);
            $table->integer('item_type')
                ->default(1)
                ->comment('1 = New, 2 = Refurb');
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
