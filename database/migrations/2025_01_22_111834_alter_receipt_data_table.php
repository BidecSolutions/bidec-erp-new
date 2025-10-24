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
        Schema::table('receipt_data', function (Blueprint $table) {
            $table->integer('rv_status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Rejected')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_data', function (Blueprint $table) {
            //
        });
    }
};
