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
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->date('bom_date');
            $table->string('bom_no', 150);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('finish_product_id');
             $table->integer('company_id');
            $table->integer('location_id');
            $table->integer('status')->default(1);

            $table->date('created_date');
            $table->string('created_by', 150);
            $table->unsignedBigInteger('user_id')->nullable(); // ✅ FIXED: no length for integer

            $table->string('approve_username', 150)->nullable();
            $table->unsignedBigInteger('approve_user_id')->nullable(); // ✅ FIXED
            $table->date('approve_date')->nullable();

            $table->string('delete_username', 150)->nullable();
            $table->unsignedBigInteger('receiver_user_id')->nullable(); // ✅ FIXED
            $table->string('receiver_username', 255)->nullable();
            $table->date('receiver_date')->nullable(); // ✅ FIXED (should be date not string)

            $table->timestamps();

            // optional: foreign key relation
            // $table->foreign('finish_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boms');
    }
};
