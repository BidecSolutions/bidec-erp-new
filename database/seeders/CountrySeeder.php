<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $username = Str::random(8);
        DB::table('countries')->insert([
            [
                'country_name' => 'Pakistan',
                'created_by' => $username,
                'created_date' => date('Y-m-d')
            ]
        ]);
    }
}
