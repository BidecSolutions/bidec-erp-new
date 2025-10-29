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
        Schema::create('store_challan', function (Blueprint $table) {
            $table->id();
            $table->integer('accounting_year');
            $table->string('material_request_no', 255);
            $table->date('material_request_date');
            $table->integer('location_id');
            $table->integer('project_id');
            $table->integer('department_id');
            $table->integer('company_id');
            $table->string('store_challan_no', 150);
            $table->date('store_challan_date');
            $table->integer('sub_department_id');
            $table->longText('description');
            $table->integer('store_challan_status')->default(1);
            $table->integer('status')->default(1);
            $table->date('date');
            $table->string('time', 20);
            $table->string('username', 150);
            $table->integer('user_id');
            $table->string('approve_username', 150);
            $table->integer('approve_user_id');
            $table->date('approve_date');
            $table->string('approve_time', 20);
            $table->string('delete_username', 150);
            $table->integer('receiver_user_id')->nullable();
            $table->string('receiver_username', 255)->nullable();
            $table->string('receiver_date', 255)->nullable();
            $table->string('receiver_time', 255)->nullable();
            $table->unsignedBigInteger('warehouse_from_id');
            $table->unsignedBigInteger('warehouse_to_id');
            $table->unsignedBigInteger('from_sub_department_id');
            $table->tinyInteger('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_challan');
    }
};
