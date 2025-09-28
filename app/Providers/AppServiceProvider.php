<?php

namespace App\Providers;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

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
            PanelsRenderHook::HEAD_END,
            function (): string {
                $color = auth()->user()->theme_color ?? '#0c5292';
            
                if ($color === 'slate' || $color === 'white' || $color === 'light' || $color === '#ffffff') {
                    $color = '#0c5292'; // fallback
                }
            
                return '<style>
                    :root {
                        --theme-color: ' . $color . ';
                    }
                    html *::selection {
                        background-color: var(--theme-color) !important;
                        /* color: #fff !important; */
                    }
                    html *::-moz-selection {
                        background-color: var(--theme-color) !important;
                        /* color: #fff !important; */
                    }
                </style>';
            },
        );

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => '<script src="' . Vite::asset('resources/js/tour.js') . '"></script>',
        );
    }
}