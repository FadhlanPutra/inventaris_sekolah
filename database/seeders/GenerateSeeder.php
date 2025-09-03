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
        // $user = User::where('email', 'admin@gmail.com')->first();
        $user = User::orderBy('id', 'asc')->first();

        if (!$user) {
            $this->command->warn("Akun untuk super_admin tidak tersedia. silahkan buat terlebih dahulu di DatabaseSeeder.php.");
            return;
        }

        $this->command->newLine();
        $this->command->warn("AKUN '{$user->email}' AKAN MENJADI SUPER_ADMIN");

        Artisan::call('shield:generate', ['--all' => true], $this->command->getOutput());
        Artisan::call('shield:super-admin', ['--user' => $user->id]);
        $this->command->info("Shield commands berhasil dijalankan untuk user {$user->email}");
        
        
        $this->command->info("Menjalankan optimasi cache...");
        Artisan::call('icons:cache');
        
        // cek cache:warm-pages
        $returnWarm = Artisan::call('cache:warm-pages');
        if ($returnWarm === 0) {
            $this->command->info("cache:warm-pages berhasil dijalankan.");
        } else {
            $this->command->error("cache:warm-pages gagal dijalankan.");
            $this->command->line(Artisan::output()); // tampilkan output error dari command
        }

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
