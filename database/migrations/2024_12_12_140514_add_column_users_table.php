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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('emp_type_multiple_campus')->default(1)->comment('1 = No, 2 = Yes')->after('id');
            $table->json('emp_ids_array')->nullable()->after('emp_id');
            $table->json('school_campus_ids_array')->nullable()->after('school_campus_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
