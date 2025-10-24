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
        Schema::table('purchase_order_datas', function (Blueprint $table) {
            $table->integer('receive_qty')->default(1)->comment('1 = No, 2 = Yes')->after('sub_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_datas', function (Blueprint $table) {
            //
        });
    }
};
