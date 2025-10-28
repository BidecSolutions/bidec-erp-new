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
        Schema::create('material_request_data', function (Blueprint $table) {
            $table->id();
            $table->integer('accounting_year');
            $table->integer('company_id');
            $table->string('material_request_no', 150);
            $table->date('material_request_date');
            $table->date('required_date');
            $table->integer('category_id');
            $table->integer('sub_item_id');
            $table->integer('uom_id');
            $table->decimal('qty', 15, 3);
            $table->decimal('approx_cost', 15, 3);
            $table->decimal('approx_sub_total', 15, 3);
            $table->text('sub_description');
            $table->integer('material_request_status')
                ->default(1)
                ->comment('1 = Pending, 2 = Approve, 3 = Rejected');
            $table->integer('store_challan_status')
                ->default(1)
                ->comment('1 = Pending, 2 = Issued');
            $table->integer('status');
            $table->date('date');
            $table->string('time', 20);
            $table->string('username', 200);
            $table->integer('user_id');
            $table->string('approve_username', 200);
            $table->string('delete_username', 200);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_request_data');
    }
};
