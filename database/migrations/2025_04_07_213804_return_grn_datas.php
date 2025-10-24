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
        //
        Schema::create('return_grn_datas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('return_good_receipt_note_id'); // FK to return GRN header
            $table->integer('po_id');
            $table->integer('po_data_id');
            $table->decimal('return_qty', 15, 3);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->text('remarks')->nullable();
            $table->integer('jv_id')->nullable(); // Journal entry (reverse) reference
            $table->string('created_by');
            $table->date('created_date');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
