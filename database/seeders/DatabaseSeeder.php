<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        
        // cek apakah user admin sudah ada
        $user = User::where('email', 'admin@gmail.com')->first();

        if (!$user) {
            $this->command->warn("User admin@gmail.com belum ada. Membuat user baru...");
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin123'),
            ]);
            $this->command->info("User admin@gmail.com berhasil dibuat.");
        } else {
            $this->command->info("User admin@gmail.com sudah ada. Skip create.");
        }
        
        $this->call([
            ShieldSeeder::class,
            GenerateSeeder::class,
            // DummyData::class,
        ]);
    }
}