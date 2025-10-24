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
        
        //

        Schema::table('faras', function (Blueprint $table) {
            //
            $table->integer('status')->default(0)->comment('1 = Sale, 2 = Purchase, 3 = Transfer Note, 4 = Purchase Return, 5 = Sale Return')->change();
            $table->string('return_order_no')->nullable()->after('rgrn_date');
            $table->date('return_order_date')->nullable()->after('return_order_no');
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
