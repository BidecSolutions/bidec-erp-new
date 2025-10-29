<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // If you have foreign key constraints, temporarily disable them for truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('menus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('menus')->insert([
            ['id' => 4,  'menu_icon' => '-', 'menu_name' => 'Menus', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 5,  'menu_icon' => '-', 'menu_name' => 'Sub Menus', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 6,  'menu_icon' => '-', 'menu_name' => 'Departments', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 9,  'menu_icon' => '-', 'menu_name' => 'Country', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 10, 'menu_icon' => '-', 'menu_name' => 'States', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 11, 'menu_icon' => '-', 'menu_name' => 'Cities', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 12, 'menu_icon' => '-', 'menu_name' => 'Role', 'menu_type' => 1, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 19, 'menu_icon' => '-', 'menu_name' => 'Company', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 21, 'menu_icon' => '-', 'menu_name' => 'Payments', 'menu_type' => 5, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 22, 'menu_icon' => '-', 'menu_name' => 'Receipts', 'menu_type' => 5, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 23, 'menu_icon' => '-', 'menu_name' => 'Journal Voucher', 'menu_type' => 5, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 24, 'menu_icon' => '-', 'menu_name' => 'Chart of Accounts', 'menu_type' => 5, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-20', 'menu_order_by' => null],
            ['id' => 26, 'menu_icon' => '-', 'menu_name' => 'Users', 'menu_type' => 1, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-05-21', 'menu_order_by' => null],
            ['id' => 33, 'menu_icon' => '-', 'menu_name' => 'Dashboard', 'menu_type' => 8, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-08-01', 'menu_order_by' => null],
            ['id' => 38, 'menu_icon' => '-', 'menu_name' => 'Reports', 'menu_type' => 7, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2024-09-09', 'menu_order_by' => null],
            ['id' => 44, 'menu_icon' => '-', 'menu_name' => 'Locations', 'menu_type' => 6, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-03', 'menu_order_by' => null],
            ['id' => 45, 'menu_icon' => '-', 'menu_name' => 'Category', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-03', 'menu_order_by' => null],
            ['id' => 46, 'menu_icon' => '-', 'menu_name' => 'Brand', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-03', 'menu_order_by' => null],
            ['id' => 47, 'menu_icon' => '-', 'menu_name' => 'Size', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-03', 'menu_order_by' => null],
            ['id' => 48, 'menu_icon' => '-', 'menu_name' => 'Product', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-06', 'menu_order_by' => null],
            ['id' => 49, 'menu_icon' => '-', 'menu_name' => 'Purchase Order', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-06', 'menu_order_by' => null],
            ['id' => 50, 'menu_icon' => '-', 'menu_name' => 'Goods Receipt Note', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-06', 'menu_order_by' => null],
            ['id' => 51, 'menu_icon' => '-', 'menu_name' => 'Payment Type', 'menu_type' => 6, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-06', 'menu_order_by' => null],
            ['id' => 52, 'menu_icon' => '-', 'menu_name' => 'Chart Of Account Setting', 'menu_type' => 5, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-07', 'menu_order_by' => null],
            ['id' => 53, 'menu_icon' => '-', 'menu_name' => 'Suppliers', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-07', 'menu_order_by' => null],
            ['id' => 54, 'menu_icon' => '-', 'menu_name' => 'Customers', 'menu_type' => 3, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-07', 'menu_order_by' => null],
            ['id' => 55, 'menu_icon' => '-', 'menu_name' => 'POS', 'menu_type' => 3, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-01-28', 'menu_order_by' => null],
            ['id' => 56, 'menu_icon' => '-', 'menu_name' => 'Direct Good Receipt Note', 'menu_type' => 2, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-02-06', 'menu_order_by' => null],
            ['id' => 57, 'menu_icon' => '-', 'menu_name' => 'Transfer Note', 'menu_type' => 4, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-02-06', 'menu_order_by' => null],
            ['id' => 58, 'menu_icon' => '-', 'menu_name' => 'Purchase Payments', 'menu_type' => 5, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-02-17', 'menu_order_by' => null],
            ['id' => 59, 'menu_icon' => '-', 'menu_name' => 'Finance', 'menu_type' => 7, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-02-18', 'menu_order_by' => null],
            ['id' => 60, 'menu_icon' => '-', 'menu_name' => 'Stock', 'menu_type' => 7, 'status' => 2, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-02-18', 'menu_order_by' => null],
            ['id' => 63, 'menu_icon' => '-', 'menu_name' => 'Direct Sale Invoice', 'menu_type' => 3, 'status' => 1, 'created_by' => 'Hassan Abdul Malik', 'created_date' => '2025-04-24', 'menu_order_by' => null],
            ['id' => 64, 'menu_icon' => '-', 'menu_name' => 'Sale Receipt', 'menu_type' => 5, 'status' => 1, 'created_by' => 'Hassan Abdul Malik', 'created_date' => '2025-04-24', 'menu_order_by' => null],
            ['id' => 65, 'menu_icon' => '-', 'menu_name' => 'Balance Sheet Report Setting', 'menu_type' => 6, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-04-28', 'menu_order_by' => null],
            ['id' => 66, 'menu_icon' => '-', 'menu_name' => 'Profit and Loss Report Setting', 'menu_type' => 6, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-04-28', 'menu_order_by' => null],
            ['id' => 67, 'menu_icon' => '-', 'menu_name' => 'Payable and Receivable Account Setting', 'menu_type' => 6, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-07-10', 'menu_order_by' => null],
            ['id' => 68, 'menu_icon' => '-', 'menu_name' => 'Purchase Invoice', 'menu_type' => 2, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-09-18', 'menu_order_by' => null],
            ['id' => 69, 'menu_icon' => '-', 'menu_name' => 'Sale Invoice', 'menu_type' => 3, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-09-18', 'menu_order_by' => null],
            ['id' => 70, 'menu_icon' => '-', 'menu_name' => 'Purchase Invoice and Payment Setting', 'menu_type' => 6, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-09-23', 'menu_order_by' => null],
            ['id' => 71, 'menu_icon' => '-', 'menu_name' => 'Sale Invoice and Receipt Setting', 'menu_type' => 6, 'status' => 1, 'created_by' => 'Shah Faisal Ranta', 'created_date' => '2025-09-23', 'menu_order_by' => null],
        ]);
    }
}
