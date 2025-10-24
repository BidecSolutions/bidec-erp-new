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
        Schema::create('transfer_note_datas', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_note_id');
            $table->integer('to_company_id');
            $table->integer('to_company_location_id');
            $table->decimal('send_qty',15,3);
            $table->decimal('receive_qty',15,3);
            $table->text('remarks')->nullable();
            $table->integer('tnd_status')->default(1)->comment('1 = Pending, 2 = Receive, 3 = Return');
            $table->integer('status')->default('1')->comment('1 = Active, 2 = Inactive');
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
        Schema::dropIfExists('transfer_note_datas');
    }
};
