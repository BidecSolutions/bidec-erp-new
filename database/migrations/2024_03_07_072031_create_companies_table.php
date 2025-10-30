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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->integer('accounting_year');
            $table->string('company_code');
            $table->string('name');
            $table->string('address');
            $table->string('contact_no');
            $table->string('dbName');
            $table->string('db_username');
            $table->string('db_password');
            $table->integer('status')->default(1)->comment('1 = Active , 2 = Inactive');
            $table->string('username');
            $table->string('time');
            $table->date('date');
            $table->string('msg_footer');
            $table->integer('sms_service_on_off')->default(1)->comment('1 = Off, 2 = On');
            $table->string('sms_service_provider');
            $table->string('masking_url');
            $table->string('masking_name');
            $table->string('masking_id');
            $table->string('masking_password');
            $table->string('masking_key');
            $table->string('school_logo');
            $table->decimal('logout_automatic_timing',15,3)->default(0);
            $table->integer('server_on_off')->default(1)->comment('1 = On, 2 = Off');
            $table->double('longitude');
            $table->double('latitude');

            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
