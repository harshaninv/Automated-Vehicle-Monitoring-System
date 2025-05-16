<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\UserInfoWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\VehicleCountbyTimeData;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament-panels::pages.dashboard';

    public function getWidgets(): array
    {
        $user = auth() -> user();
        $widgets = [];

        if ($user -> role == 'vehicle_owner'){
            $widgets[] = UserInfoWidget::class;
        }
        else {
            $widgets[] = UserInfoWidget::class;
            $widgets[] = StatsOverview::class;
            $widgets[] = VehicleCountbyTimeData::class;
        }

        return $widgets;
    }

}
