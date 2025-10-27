<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_locations')->insert([
            [
                'id' => 1,
                'company_id' => 1,
                'location_code' => '-',
                'name' => 'Location One',
                'phone_no' => '-',
                'email' => 'demo-company@gmail.com',
                'address' => '-',
                'status' => 1,
                'created_by' => 'Admin',
                'created_date' => '2025-10-24',
            ],
        ]);
    }
}
