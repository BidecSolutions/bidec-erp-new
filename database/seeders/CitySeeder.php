<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\States;
use DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = States::all(); // Assuming you have a Country model
        $username = Str::random(8);
        $cities = [
            ['city_name' => 'Karachi', 'state_id' => $states->random()->id,'created_by' => $username,'created_date' => date('Y-m-d')],
            ['city_name' => 'Badin', 'state_id' => $states->random()->id,'created_by' => $username,'created_date' => date('Y-m-d')],
            ['city_name' => 'Gharo', 'state_id' => $states->random()->id,'created_by' => $username,'created_date' => date('Y-m-d')],
            ['city_name' => 'Golarchi', 'state_id' => $states->random()->id,'created_by' => $username,'created_date' => date('Y-m-d')],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert($city);
        }
    }
}
