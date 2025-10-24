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
        Schema::table('sub_menus', function (Blueprint $table) {
            $table->integer('sub_menu_type')->default(1)->comment('1 = Front Nav, 2 = Inside Page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_menus', function (Blueprint $table) {
            $table->dropColumn('sub_menu_type');
        });
    }
};
