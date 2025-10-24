<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Country;
use DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = Country::all(); // Assuming you have a Country model
        $username = Str::random(8);
        $states = [
            ['state_name' => 'Sindh', 'country_id' => $countries->random()->id,'created_by' => $username,'created_date' => date('Y-m-d')],
        ];

        foreach ($states as $state) {
            DB::table('states')->insert($state);
        }
    }
}
