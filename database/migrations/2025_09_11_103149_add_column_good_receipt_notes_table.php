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
            $table->integer('tax_account_id')->nullable()->after('description');
            $table->decimal('tax_amount',15,3)->default(0)->after('tax_account_id');
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
