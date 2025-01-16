<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use App\Services\Filament\Login;
use App\Services\Filament\Register;
use App\Filament\Widgets\WorkerStat;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\DashboardWidget;
use App\Filament\Widgets\DepositWidget;
use App\Filament\Pages\EditSetting;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            
            // ->registration(Register::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->authGuard('web')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->brandLogo('https://panel.mt-panel.guru/images/logo.svg')
            ->darkModeBrandLogo('https://panel.mt-panel.guru/images/logo-dark.svg')
            ->brandLogoHeight('3rem')
            ->pages([
                Pages\Dashboard::class,
                EditSetting::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
                DashboardWidget::class,
                DepositWidget::class,
                WorkerStat::class,
            ])
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
            ]);
    }
}
