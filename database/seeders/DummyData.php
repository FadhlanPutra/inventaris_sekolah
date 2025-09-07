<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Electronics'],
            ['name' => 'Multimedia'],
            ['name' => 'Lab Tools'],
        ]);


        DB::table('inventories')->insert([
            [
                'category_id' => 1,
                'item_name' => 'Laptop ASUS ROG',
                'category' => 'Electronics',
                'quantity' => 5,
                'status' => 'available',
                'desc' => 'High-performance laptop for gaming and research.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'item_name' => 'Projector Epson',
                'category' => 'Multimedia',
                'quantity' => 2,
                'status' => 'unavailable',
                'desc' => 'Used for classroom presentations.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'item_name' => 'Arduino Kit',
                'category' => 'Lab Tools',
                'quantity' => 10,
                'status' => 'available',
                'desc' => 'Starter kit for IoT and robotics projects.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


         DB::table('borrows')->insert([
            [
                'user_id' => 1,
                'item_id' => 2,
                'borrow_time' => Carbon::now()->subDays(2),
                'return_time' => Carbon::now()->addDays(5),
                'labusage_id' => 1,
                'quantity' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'item_id' => 1,
                'borrow_time' => Carbon::now()->subDays(1),
                'return_time' => null,
                'labusage_id' => 2,
                'quantity' => 1,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        DB::table('maintenances')->insert([
            [
                'inventory_id' => 1,
                'condition' => 'Damaged',
                'breaking' => 'Broken screen',
                'condition_before' => 'Cracked but usable',
                'condition_after' => 'Replaced screen, now working',
                'add_notes' => 'Took 3 days to repair',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_id' => 2,
                'condition' => 'Needs Service',
                'breaking' => 'Overheating',
                'condition_before' => 'Overheats after 30 mins',
                'condition_after' => null,
                'add_notes' => 'Waiting for spare parts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_id' => 3,
                'condition' => 'Good',
                'breaking' => 'Dust buildup',
                'condition_before' => 'Fan clogged',
                'condition_after' => 'Cleaned, works fine',
                'add_notes' => 'Next check in 6 months',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        DB::table('lab_usages')->insert([
            [
                'user_id' => 1,
                'num_lab' => 2,
                'lab_function' => 'Computer Programming Class',
                'end_state' => 'All PCs working fine',
                'notes' => 'Used for final project practice',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'num_lab' => 3,
                'lab_function' => 'Electronics Workshop',
                'end_state' => '2 kits damaged',
                'notes' => 'Overloaded Arduino boards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'num_lab' => 1,
                'lab_function' => 'Networking Practice',
                'end_state' => 'Switch rebooted',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
