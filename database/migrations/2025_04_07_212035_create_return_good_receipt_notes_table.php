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
        Schema::create('return_good_receipt_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('good_receipt_note_id'); // FK to original GRN
            $table->string('return_grn_no'); // Unique return GRN number
            $table->date('return_date');
            $table->integer('supplier_id');
            $table->text('reason')->nullable();
            $table->integer('status')->default('1')->comment('1 = Active, 2 = Inactive');
            $table->integer('return_grn_status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Reject');
            $table->string('created_by');
            $table->date('created_date');
            $table->string('approved_by')->nullable();
            $table->date('approved_date')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_good_receipt_notes');
    }
};
