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
        Schema::table('chart_of_account_settings', function (Blueprint $table) {
            $table->integer('option_id')->comment('1 = Category and Sub Category, 2 = Customers , 3 = Suppliers	,4 = Bank Account, 5 = Cash Account')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_account_settings', function (Blueprint $table) {
            //
        });
    }
};
