<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class WarmPageCache extends Command
{
    protected $signature = 'cache:warm-pages';
    protected $description = 'Pre-warm page caches by visiting important URLs';

    public function handle()
    {
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
            
            route('password.request'),
            route('filament.dashboard.pages.themes'),
            route('filament.dashboard.pages.edit-profile'),
        ];

        foreach ($urls as $url) {
            try {
                Http::get($url);
                $this->info("Cache warmed for: {$url}");
            } catch (\Exception $e) {
                $this->error("Gagal warming: {$url} | Error: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Warming cache finished.");
    }
}
