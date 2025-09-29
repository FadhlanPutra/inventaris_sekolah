<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Grade;
use App\Models\Borrow;
use App\Models\Category;
use App\Models\LabUsage;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\Maintenance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grade::insert([
            [
                'name' => 'X RPL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'X DKV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'X TKJ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XI RPL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XI DKV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XI TKJ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XII RPL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XII DKV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'XII TKJ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Location::insert([
            [
                'name' => 'Lab 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab 4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab 5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab 6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Category::insert([
            [
                'name' => 'Electronics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Multimedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab Tools',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Inventory::insert([
            [
                'item_name' => 'Laptop ASUS ROG',
                'category_id' => 1,
                'quantity' => 5,
                'location_id' => 1,
                'status' => 'Available',
                'desc' => 'High-performance laptop for gaming and research.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Projector Epson',
                'category_id' => 2,
                'quantity' => 2,
                'location_id' => 3,
                'status' => 'Unavailable',
                'desc' => 'Used for classroom presentations.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Arduino Kit',
                'category_id' => 3,
                'quantity' => 10,
                'location_id' => 2,
                'status' => 'Available',
                'desc' => 'Starter kit for IoT and robotics projects.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        LabUsage::insert([
            [
                'user_id' => 1,
                'status' => 'Complete',
                'grade_id' => 1,
                'num_students' => 25,
                'location_id' => 1,
                'lab_function' => 'Computer Programming Class',
                'end_state' => 'All PCs working fine',
                'notes' => 'Used for final project practice',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'Incomplete',
                'grade_id' => 4,
                'num_students' => null,
                'location_id' => 5,
                'lab_function' => 'Electronics Workshop',
                'end_state' => '2 kits damaged',
                'notes' => 'Overloaded Arduino boards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'Complete',
                'grade_id' => 2,
                'num_students' => 24,
                'location_id' => 2,
                'lab_function' => 'Networking Practice',
                'end_state' => 'Switch rebooted',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Borrow::insert([
            [
                'user_id' => 1,
                'item_id' => 2,
                'quantity' => 3,
                'location_id' => 1,
                'borrow_time' => Carbon::now()->subDays(2),
                'return_time' => Carbon::now()->addDays(5),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'item_id' => 1,
                'quantity' => 1,
                'location_id' => 2,
                'borrow_time' => Carbon::now()->subDays(1),
                'return_time' => null,
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Maintenance::insert([
            [
                'inventory_id' => 1,
                'issue' => 'Broken screen',
                'condition_before' => 'Cracked but usable',
                'condition_after' => 'Replaced screen, now working',
                'add_notes' => 'Took 3 days to repair',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_id' => 2,
                'issue' => 'Overheating',
                'condition_before' => 'Overheats after 30 mins',
                'condition_after' => null,
                'add_notes' => 'Waiting for spare parts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'inventory_id' => 3,
                'issue' => 'Dust buildup',
                'condition_before' => 'Fan clogged',
                'condition_after' => 'Cleaned, works fine',
                'add_notes' => 'Next check in 6 months',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
