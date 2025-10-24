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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('nazim_id')->default(0)->comment('Employee Id')->after('nazim_e_talimat');
            $table->string('naib_nazim_id')->default(0)->comment('Employee Id')->after('nazim_id');
            $table->string('moavin_id')->default(0)->comment('Employee Id')->after('naib_nazim_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('nazim_id');
            $table->dropColumn('naib_nazim_id');
            $table->dropColumn('moavin_id');
        });
    }
};
