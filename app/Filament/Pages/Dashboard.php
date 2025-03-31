<?php

namespace App\Filament\Pages;

use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $view = 'filament-panels::pages.dashboard';


    // UserInfoWidget is a custom widget that displays the user's details
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\UserInfoWidget::class,
        ];
    }
    
}

