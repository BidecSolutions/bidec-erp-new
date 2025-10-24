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
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('pi_id')->default(0)->after('grn_id');
            $table->integer('entry_option')->default(1)->comment('1 = Normal,2 = Purchase,3 = Purchase Invoice')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('pi_id');
            $table->integer('entry_option')->default(1)->comment('1=Normal,2=Purchase')->change();
        });
    }
};

