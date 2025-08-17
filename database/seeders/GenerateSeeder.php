<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class GenerateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@gmail.com')->first();

        if (!$user) {
            $this->command->error("User admin@gmail.com tidak ditemukan.");
            return;
        }

        Artisan::call('shield:generate', ['--all' => true], $this->command->getOutput());
        Artisan::call('shield:super-admin', ['--user' => $user->id]);
        Artisan::call('icons:cache');

        $this->command->info("Shield commands berhasil dijalankan untuk user {$user->email}");
    }
}
