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
            $table->integer('menu_type')->default(1)->comment('1 = User, 2 = Finance, 3 = Purchase, 4 = Store, 5 = Sale, 6 = HR, 7 = Reports, 8 = Dashboard, 9 = General Setting, 10 = General Option')->change();
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
