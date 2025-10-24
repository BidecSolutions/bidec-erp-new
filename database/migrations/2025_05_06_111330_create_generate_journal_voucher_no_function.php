<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateGenerateJournalVoucherNoFunction extends Migration
{
    public function up()
    {
        DB::unprepared("
            DROP FUNCTION IF EXISTS generate_journal_voucher_no;

            CREATE FUNCTION generate_journal_voucher_no()
            RETURNS VARCHAR(20)
            DETERMINISTIC
            BEGIN
                DECLARE prefix VARCHAR(5) DEFAULT 'JV';
                DECLARE next_number INT;
                DECLARE padded_date VARCHAR(4);
                DECLARE voucher_no VARCHAR(20);

                SET padded_date = DATE_FORMAT(CURDATE(), '%m%y');

                SELECT 
                    IFNULL(MAX(CONVERT(SUBSTRING(jv_no, 3, LENGTH(jv_no) - 6), UNSIGNED)), 0) + 1 
                INTO next_number
                FROM journal_vouchers
                WHERE SUBSTRING(jv_no, -4, 2) = DATE_FORMAT(CURDATE(), '%m')
                  AND SUBSTRING(jv_no, -2, 2) = DATE_FORMAT(CURDATE(), '%y');

                SET voucher_no = CONCAT(prefix, next_number, padded_date);

                RETURN voucher_no;
            END;
        ");
    }

    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS generate_journal_voucher_no;");
    }
}

