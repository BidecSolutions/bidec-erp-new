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

        Schema::table('menus', function (Blueprint $table) {
            $table->integer('menu_type')->default(1)->comment('1 = User, 2 = Purchase, 3 = Sales, 4 = Store, 5 = Finance, 6 = Setting, 7 = Reports, 8 = Dashboard, 9 = HR')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            //
        });
    }
};
