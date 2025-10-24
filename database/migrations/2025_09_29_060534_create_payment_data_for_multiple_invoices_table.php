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
        Schema::create('payment_data_for_multiple_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_id');
            $table->integer('pi_id');
            $table->decimal('paid_amount',15,3);
            $table->integer('status')->comment('1 = Active, 2 = Inactive');
            $table->string('created_by');
            $table->date('created_date');
            $table->string('updated_by')->nullable();
            $table->date('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_data_for_multiple_invoices');
    }
};
