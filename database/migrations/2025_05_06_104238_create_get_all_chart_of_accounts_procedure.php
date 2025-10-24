<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateGetAllChartOfAccountsProcedure extends Migration
{
    public function up()
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS GetAllChartOfAccounts;
            CREATE PROCEDURE GetAllChartOfAccounts(IN input_status VARCHAR(50), IN input_company_id INT)
            BEGIN
                SELECT id, code, name
                FROM chart_of_accounts
                WHERE (status = input_status OR input_status = '')
                  AND company_id = input_company_id
                ORDER BY level1 ASC, level2 ASC, level3 ASC, level4 ASC, level5 ASC, level6 ASC, level7 ASC;
            END
        ");
    }

    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS GetAllChartOfAccounts;");
    }
}

