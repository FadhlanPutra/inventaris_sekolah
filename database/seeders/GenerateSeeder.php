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

        // Jalankan npm install
        $this->command->warn("Menjalankan npm install...");
        exec('npm install 2>&1', $outputInstall, $returnInstall);
            
        if ($returnInstall === 0) {
            $this->command->info("npm install berhasil!");
        } else {
            $this->command->error("npm install gagal.");
            $this->command->line(implode("\n", $outputInstall));
            return; // berhenti jika gagal install
        }
        
        // Setelah install sukses, lanjut build
        $this->command->warn("Menjalankan npm run build...");
        exec('npm run build 2>&1', $outputBuild, $returnBuild);
        
        if ($returnBuild === 0) {
            $this->command->info("npm run build berhasil!");
        } else {
            $this->command->error("npm run build gagal.");
            $this->command->line(implode("\n", $outputBuild));
        }

    }
}
