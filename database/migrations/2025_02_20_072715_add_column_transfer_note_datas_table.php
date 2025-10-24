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
            $table->decimal('return_qty',15,3)->default(0)->after('receive_qty');
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
