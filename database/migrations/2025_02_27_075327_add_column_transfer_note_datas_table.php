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
        Schema::table('transfer_note_datas', function (Blueprint $table) {
            $table->integer('tn_status')->default(1)->comment('1 = Pending, 2 = Approve, 3 = Reject')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_note_datas', function (Blueprint $table) {
            //
        });
    }
};
