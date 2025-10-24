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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->integer('type')->nullable()->comment('1 = Task, 2 = Test, 3 = General');
            $table->text('data'); // JSON data for the notification
            $table->timestamp('read_at')->nullable(); // Timestamp for when the notification is read
            $table->timestamps(); // Created at and updated at timestamps

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
