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

        // Jalankan npm run build
        $this->command->warn("Menjalankan npm run build...");
        exec('npm run build 2>&1', $output, $returnVar);

        if ($returnVar === 0) {
            $this->command->info("npm run build berhasil dijalankan!");
        } else {
            $this->command->error("npm run build gagal dijalankan.");
            $this->command->line(implode("\n", $output));
        }
    }
}
