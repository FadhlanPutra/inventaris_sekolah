<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\ThemesPlugin;
use Filament\Navigation\NavigationItem;
use App\Filament\Dashboard\Themes\Pesat;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Rmsramos\Activitylog\ActivitylogPlugin;
use JibayMcs\FilamentTour\FilamentTourPlugin;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Notifications\Livewire\Notifications;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Pages\Auth\EmailVerificationPromptCustom;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('dashboard')
            ->path('dashboard')
            // app_asset() ada di app/Helpers/helpers.php
            ->brandLogo(app_asset('images/logo.png'))
            ->favicon(app_asset('images/logo_x.png'))
            ->brandLogoHeight('100px')
            ->default()
            ->registration()
            ->emailVerification(EmailVerificationPromptCustom::class)
            ->passwordReset()
            ->login()
            ->colors([
                'primary' => Color::Yellow,
            ])
            // Hentikan discoverPages otomatis supaya kontrol penuh
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class, // Hanya dashboard yang muncul di sidebar
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Edit Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-o-user-circle'),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Filament Shield'),            
                NavigationGroup::make()
                    ->label('Users')
                    ->collapsed(),
            ])
            ->sidebarCollapsibleOnDesktop()
            // ->sidebarFullyCollapsibleOnDesktop()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // ->spa()
            ->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseNotificationsPolling('20s')
            ->plugins([
                FilamentShieldPlugin::make(),
                ActivitylogPlugin::make()
                    ->label('Log')
                    ->navigationCountBadge(true)
                    ->navigationGroup('Activity Log')
                    ->pluralLabel('Logs')
                    ->authorize(
                        fn () => auth()->user()->can('access_log')
                    ),
                FilamentTourPlugin::make(),
                FilamentEditProfilePlugin::make()
                        ->shouldRegisterNavigation(false)
                        ->setIcon('heroicon-o-user')
                        ->setSort(10)
                        ->shouldShowAvatarForm(
                            value: true,
                            directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                            rules: 'mimes:jpeg,jpg,png,webp|max:2048'
                        ),
                ThemesPlugin::make()
                    ->registerTheme([
                        Pesat::getName() => Pesat::class,
                    ])
            ]);
    }
}
