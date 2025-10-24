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
        Schema::create('good_receipt_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('supplier_id');
            $table->string('grn_no');
            $table->date('grn_date');
            $table->text('description');
            $table->integer('status')->default('1')->comment('1 = Active, 2 = Inactive');
            $table->integer('grn_status')->default('1')->comment('1 = Pending, 2 = Approve');
            $table->string('created_by');
            $table->date('created_date');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_receipt_notes');
    }
};
