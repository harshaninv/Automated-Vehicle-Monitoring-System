<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Register;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
//            ->path('admin')
            ->login()
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->brandName('AVMS')
            // ->brandLogo('images/logo.png')
            ->colors([
                'gray' => [
                    '50' => '#e0f7f5',
                    '100' => '#009e92',
                    '200' => '#00b8ab',
                    '300' => '#4245aa',
                    '400' => '#00b8ab',
                    '500' => '#005775',
                    '600' => '#8697ff',
                    '700' => '#005775',
                    '800' => '#bfcaff',
                    '900' => '#012322',
                    '950' => '#002e2e',
                ],

                'danger' => [
                    '50' => '#a83232',
                    '100' => '#003d3a',
                    '200' => '#005c56',
                    '300' => '#007d74',
                    '400' => '#009e92',
                    '500' => '#00b8ab',
                    '600' => '#e6faf9',
                    '700' => '#4dd3c9',
                    '800' => '#80dfd8',
                    '900' => '#b2ebe7',
                    '950' => '#e0f7f5',
                ],
                'info' => [
                    '50' => '#00354a',
                    '100' => '#00202e',
                    '200' => '#005775',
                    '300' => '#007aa3',
                    '400' => '#009dd1',
                    '500' => '#00bfff',
                    '600' => '#009e78',
                    '700' => '#4dd6ff',
                    '800' => '#80e1ff',
                    '900' => '#b3ecff',
                    '950' => '#e0f7ff',

                ],
                'primary' => [
                    '50' => '#92e6e3',
                    '100' => '#00354d',
                    '200' => '#005578',
                    '300' => '#0078a5',
                    '400' => '#009ed4',
                    '500' => '#26bbff',
                    '600' => '#005578',
                    '700' => '#75d3ff',
                    '800' => '#9ddfff',
                    '900' => '#c3ecff',
                    '950' => '#e7f8ff',
                ],
               'success' => [
                    '50' => '#005a45',
                    '100' => '#003c2e',
                    '200' => '#005a45',
                    '300' => '#007c5e',
                    '400' => '#009e78',
                    '500' => '#11c8bf',
                    '600' => '#e6faf9',
                    '700' => '#4dd5b7',
                    '800' => '#80e1cb',
                    '900' => '#b3ede0',
                    '950' => '#e0f9f4',
                ],
                'warning' => [
                    '50' => '#e36f10',
                    '100' => '#033d3b',
                    '200' => '#055c59',
                    '300' => '#08827d',
                    '400' => '#0da9a1',
                    '500' => '#11c8bf',
                    '600' => '#e6faf9',
                    '700' => '#65dcd7',
                    '800' => '#92e6e3',
                    '900' => '#bdf0ef',
                    '950' => '#e6faf9',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }
}
