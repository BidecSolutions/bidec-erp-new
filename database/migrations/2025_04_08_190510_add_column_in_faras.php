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
        Schema::table('faras', function (Blueprint $table) {
            //
            $table->integer('status')->default(0)->comment('1 = Sale, 2 = Purchase, 3 = Transfer Note, 4 = Return')->change();
            $table->string('rgrn_no')->nullable()->after('grn_date');
            $table->date('rgrn_date')->nullable()->after('rgrn_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faras', function (Blueprint $table) {
            //
        });
    }
};
