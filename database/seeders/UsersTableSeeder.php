<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'emp_type_multiple_campus' => 1,
                'emp_id' => 0,
                'emp_ids_array' => null,
                'student_id' => null,
                'student_ids_array_for_parents' => null,
                'acc_type' => 'client',
                'company_id' => '1',
                'company_location_id' => null,
                'school_campus_ids_array' => null,
                'username' => 'admin',
                'mobile_no' => '',
                'cnic_no' => '',
                'sgpe' => '',
                'name' => 'Admin',
                'email' => 'ushahfaisalranta@gmail.com',
                'device_tokens' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$lCHvyfaq2GOltldu0i6RXePBo8UurKsQJ2uHin59nmq/TPoVfY5eC', // already hashed
                'status' => '1',
                'fcm_token' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'suspended' => 2,
            ],
        ]);
    }
}
