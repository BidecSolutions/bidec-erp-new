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
        Schema::create('store_challan_return', function (Blueprint $table) {
            $table->id();
            $table->integer('accounting_year');
            $table->string('slip_no', 150);
            $table->string('store_challan_return_no', 150);
            $table->date('store_challan_return_date');
            $table->integer('sub_department_id');
            $table->unsignedBigInteger('company_id')->default(0);
            $table->unsignedBigInteger('project_id')->default(0);
            $table->unsignedBigInteger('location_id')->default(0);
            $table->unsignedBigInteger('department_id')->default(0);
            $table->longText('description');
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
        Schema::dropIfExists('store_challan_return');
    }
};
