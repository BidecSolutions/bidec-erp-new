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
        Schema::create('education_detail_for_employees', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_id');
            $table->integer('hafiz')->default(0)->comment('1 = Yes, 0 = No');
            $table->integer('')->default(0)->comment('1 = Yes, 0 = No');
            $table->integer('')->default(0)->comment('1 = Yes, 0 = No');
            $table->integer('')->default(0)->comment('1 = Yes, 0 = No');
            $table->integer('')->default(0)->comment('1 = Yes, 0 = No');
            $table->string('teacher_name');
            $table->string('asri_taleem');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_detail_for_employees');
    }
};
