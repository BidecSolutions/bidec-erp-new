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
        Schema::create('bom_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bom_id');
            $table->unsignedBigInteger('row_product_id');
            $table->decimal('qty', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->date('created_date');
            $table->string('created_by', 150);
            $table->integer('user_id');
            $table->string('approve_username', 150)->nullable();
            $table->date('approve_date')->nullable();
            $table->string('delete_username', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_datas');
    }
};
