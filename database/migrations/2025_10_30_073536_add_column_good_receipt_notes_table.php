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
        Schema::table('good_receipt_notes', function (Blueprint $table) {
            $table->integer('payment_status')->default(1)->comment('1 = Pending, 2 = Complete Payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('good_receipt_notes', function (Blueprint $table) {
            //
        });
    }
};
