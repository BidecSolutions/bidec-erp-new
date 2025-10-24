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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->string('department_name');
            $table->integer('department_fees');
            $table->string('department_timing');
            $table->string('head_of_department');
            $table->date('department_start_date');
            $table->integer('no_of_student');
            $table->integer('status')->default(1);
            $table->string('created_by');
            $table->date('created_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
