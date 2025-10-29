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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('location_id');
            $table->string('material_request_no', 150);
            $table->date('material_request_date');
            $table->integer('department_id');
            $table->text('main_description');
            $table->integer('material_request_status')
                ->default(1)
                ->comment('1 = Pending, 2 = Approve, 3 = Rejected');
            $table->integer('status');
            $table->date('created_date');
            $table->string('created_by', 200);
            $table->integer('user_id');
            $table->string('approve_username', 200)->nullable();
            $table->date('approve_date')->nullable();
            $table->integer('approve_user_id')->nullable();
            $table->string('delete_username', 200)->nullable();
            $table->date('delete_date')->nullable();
            $table->integer('delete_user_id')->nullable();
            $table->integer('store_challan_status')
                ->default(1)
                ->comment('1 = Pending, 2 = Issued');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_request');
    }
};
