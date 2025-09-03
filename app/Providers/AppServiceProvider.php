<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $has8000 = request()->getPort() === 8000;

        if (app()->environment('production') || 
            // str_contains(request()->getHost(), 'tprojectlan.my.id')
            ! $has8000
            )
        {
            URL::forceScheme('https');
        }

        Gate::policy(\Spatie\Permission\Models\Role::class, \App\Policies\RolePolicy::class);

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => '<script src="' . Vite::asset('resources/js/tour.js') . '"></script>',
        );
    }
}