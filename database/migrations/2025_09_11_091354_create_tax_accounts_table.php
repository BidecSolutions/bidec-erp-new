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
        Schema::create('tax_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('company_location_id');
            $table->unsignedBigInteger('acc_id');
            $table->string('name');
            $table->decimal('percent_value', 15, 3);
            $table->tinyInteger('status')->comment('1 = Active, 2 = Inactive')->default(1);
            $table->string('created_by');
            $table->date('created_date');
            $table->timestamps(); // optional, but recommended
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_accounts');
    }
};
