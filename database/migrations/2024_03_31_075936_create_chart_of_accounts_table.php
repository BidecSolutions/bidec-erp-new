<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->string('code', 255)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->string('parent_code', 22)->default('0');
            $table->integer('level1')->default(0);
            $table->integer('level2')->default(0);
            $table->integer('level3')->default(0);
            $table->integer('level4')->default(0);
            $table->integer('level5')->default(0);
            $table->integer('level6')->default(0);
            $table->integer('level7')->default(0);
            $table->longText('name');
            $table->integer('status')->default(1)->comment('1 = Active, 2 = Inactive');
            $table->string('username');
            $table->date('date');
            $table->string('time');
            $table->integer('trail_id')->default(0);
            $table->integer('operational')->default(1)->comment('1 = Operational , 2 = Not Operational');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
