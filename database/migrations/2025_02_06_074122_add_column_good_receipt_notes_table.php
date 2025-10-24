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
            $table->integer('process_type')->default(1)->comment('1 = Normal, 2 = Direct Good Receipt Note')->after('company_location_id');
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
