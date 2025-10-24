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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('company_location_id');
            $table->integer('parent_id')->default(0)->nullable();
            $table->integer('attribute_id')->default(0)->nullable();
            $table->integer('acc_id')->default(0)->nullable();
            $table->integer('brand_id')->default(0)->nullable();
            $table->string('name')->nullable();
            $table->integer('order_number')->default(0)->nullable();
            $table->integer('level')->default(0)->nullable();
            $table->string('banner_image')->nullable();
            $table->string('icon_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
            $table->string('created_by');
            $table->date('created_date');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
