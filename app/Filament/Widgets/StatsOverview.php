<?php

namespace App\Filament\Widgets;

use App\Models\TimeLog;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now();

        // Total Vehicles In today
        $totalVehiclesIn = TimeLog::whereDate('time_in', $today)->count();

        // Total Vehicles In today that belong to Students
        $studentVehiclesIn = TimeLog::whereDate('time_in', $today)
            ->whereHas('vehicle.owner', function ($query) {
                $query->whereHas('student');
            })
            ->count();

        // Total Vehicles Out today
        $totalVehiclesOut = TimeLog::whereDate('time_out', $today)->count();

        // Total Vehicles Out today that belong to Students
        $studentVehiclesOut = TimeLog::whereDate('time_out', $today)
            ->whereHas('vehicle.owner', function ($query) {
                $query->whereHas('student');
            })
            ->count();

        return [
            Stat::make('Total Vehicles In', $totalVehiclesIn)
                ->icon('heroicon-o-truck')
                ->description("Students' Vehicles In: {$studentVehiclesIn}")
                ->descriptionIcon('heroicon-o-information-circle'),

            Stat::make('Total Vehicles Out', $totalVehiclesOut)
                ->icon('heroicon-o-truck')
                ->description("Students' Vehicles Out: {$studentVehiclesOut}")
                ->descriptionIcon('heroicon-o-information-circle'),
        ];
    }
}
