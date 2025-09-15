<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Artisan;

class WarmPageCache extends Command
{
    protected $signature = 'cache:warm-pages {--fresh : Clear cache before warming}';
    protected $description = 'Pre-warm page caches by visiting important URLs';

    public function handle()
    {
        if ($this->option('fresh')) {
            $this->warn("Clearing response cache...");
            Artisan::call('responsecache:clear');
        }

        $urls = [
            route('filament.dashboard.auth.login'),
            route('filament.dashboard.auth.register'),
            
            route('filament.dashboard.pages.dashboard'),
            route('filament.dashboard.resources.borrows.index'),
            route('filament.dashboard.resources.categories.index'),
            route('filament.dashboard.resources.inventories.index'),
            route('filament.dashboard.resources.lab-usages.index'),
            route('filament.dashboard.resources.maintenances.index'),
            route('filament.dashboard.resources.shield.roles.index'),
            route('filament.dashboard.resources.users.index'),

            route('filament.dashboard.resources.borrows.create'),
            route('filament.dashboard.resources.categories.create'),
            route('filament.dashboard.resources.inventories.create'),
            route('filament.dashboard.resources.lab-usages.create'),
            route('filament.dashboard.resources.maintenances.create'),
            route('filament.dashboard.resources.shield.roles.create'),
            route('filament.dashboard.resources.users.create'),
            
            route('filament.dashboard.pages.themes'),
            route('filament.dashboard.pages.edit-profile'),

            url('flux/flux.js'),
            url('flux/flux.min.js'),
            url('livewire/livewire.js'),

            url('images/logo.png'),
            url('images/logo_x.png'),

            Vite::asset('resources/css/app.css'),
            Vite::asset('resources/js/app.js'),
            Vite::asset('resources/js/tour.js'),
            Vite::asset('resources/js/darkMode.js'),
            Vite::asset('resources/css/filament/dashboard/themes/pesat.css'),
        ];

        $responses = Http::pool(fn ($pool) =>
            array_map(fn ($url) => $pool->get($url), $urls)
        );
            
        foreach ($urls as $i => $url) {
            $response = $responses[$i];
        
            if ($response instanceof \Illuminate\Http\Client\Response) {
                if ($response->successful()) {
                    $this->info("Cache warmed for: {$url}");
                } else {
                    $this->error("Gagal warming: {$url} | Status: {$response->status()}");
                }
            } else {
                // Kalau gagal total (ConnectionException, dll)
                $this->error("Gagal warming: {$url} | Connection error");
            }
        }

        $this->newLine();
        $this->info("Warming cache finished.");
    }
}
