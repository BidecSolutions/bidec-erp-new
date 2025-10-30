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
        Schema::create('store_challans', function (Blueprint $table) {
            $table->id();
            $table->integer('material_request_id');
            $table->integer('company_id');
            $table->integer('location_id');
            $table->integer('department_id');
            $table->string('store_challan_no', 150);
            $table->date('store_challan_date');
            $table->longText('description');
            $table->integer('store_challan_status')->default(1);
            $table->integer('status')->default(1);
            $table->date('created_date');
            $table->string('created_by', 150);
            $table->integer('user_id');
            $table->string('approve_username', 150)->nullable();
            $table->integer('approve_user_id')->nullable();
            $table->date('approve_date')->nullable();
            $table->string('delete_username', 150)->nullable();
            $table->integer('receiver_user_id')->nullable();
            $table->string('receiver_username', 255)->nullable();
            $table->string('receiver_date', 255)->nullable();
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
