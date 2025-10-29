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
            $table->integer('material_request_id');
            $table->date('required_date');
            $table->integer('product_variant_id');
            $table->decimal('qty', 15, 3);
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
            $table->string('approve_username', 200)->nullable();
            $table->string('delete_username', 200)->nullable();
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
