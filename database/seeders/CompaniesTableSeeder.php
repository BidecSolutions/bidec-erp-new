<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'id' => 1,
                'registration_no' => '01',
                'accounting_year' => 0,
                'company_code' => '01',
                'name' => 'Demo Company',
                'address' => '-',
                'contact_no' => '-',
                'nazim_e_talimat' => '-',
                'nazim_id' => '-',
                'naib_nazim_id' => '-',
                'moavin_id' => '-',
                'dbName' => '-',
                'db_username' => '-',
                'db_password' => '-',
                'school_logo' => '-',
                'status' => 1,
                'username' => 'Admin',
                'time' => '10:28:41',
                'date' => '2025-10-24',
                'msg_footer' => '-',
                'sms_service_on_off' => 1,
                'sms_service_provider' => '-',
                'masking_url' => '-',
                'masking_name' => '-',
                'masking_id' => '-',
                'masking_password' => '-',
                'masking_key' => '-',
                'logout_automatic_timing' => 0.000,
                'server_on_off' => 1,
                'longitude' => 0,
                'latitude' => 0,
            ],
        ]);
    }
}
