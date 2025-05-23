<?php

namespace App\Filament\Widgets;

use App\Models\TimeLog;
use App\Models\User;
use App\Models\Guest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $today = now();

        // Total Vehicles In today
        $totalVehiclesIn = TimeLog::whereDate('time_in', $today)->count();

        // Total Vehicles In today that belong to Students
        $studentVehiclesIn = TimeLog::whereDate('time_in', $today)
        ->whereHas('vehicle', function ($query) {
            $query->where('owner_type', User::class)
                ->whereHasMorph('owner', [User::class], function ($q) {
                    $q->whereHas('student');
                });
        })
        ->count();


        // Total Vehicles Out today
        $totalVehiclesOut = TimeLog::whereDate('time_out', $today)->count();

        // Total Vehicles Out today that belong to Students
        $studentVehiclesOut = TimeLog::whereDate('time_out', $today)
        ->whereHas('vehicle', function ($query) {
            $query->where('owner_type', User::class)
                ->whereHasMorph('owner', [User::class], function ($q) {
                    $q->whereHas('student');
                });
        })
        ->count();

        // Total Vehicles In today that belong to Staff
        $staffVehiclesIn = TimeLog::whereDate('time_in', $today)
        ->whereHas('vehicle', function ($query) {
            $query->where('owner_type', User::class)
                ->whereHasMorph('owner', [User::class], function ($q) {
                    $q->whereHas('staff');
                });
        })
        ->count();
        
        // Total Vehicles Out today that belong to Staff
        $staffVehiclesOut = TimeLog::whereDate('time_out', $today)
        ->whereHas('vehicle', function ($query) {
            $query->where('owner_type', User::class)
                ->whereHasMorph('owner', [User::class], function ($q) {
                    $q->whereHas('staff');
                });
        })
        ->count();
        

// GUEST vehicles (use direct owner_type match)
$guestVehiclesIn = TimeLog::whereDate('time_in', $today)
    ->whereHas('vehicle', function ($query) {
        $query->where('owner_type', Guest::class);
    })
    ->count();

$guestVehiclesOut = TimeLog::whereDate('time_out', $today)
    ->whereHas('vehicle', function ($query) {
        $query->where('owner_type', Guest::class);
    })
    ->count();

        // number of remaing vehicles in university calculated by subtracting the total vehicles in from the total vehicles out for each user type guest, staff, student
        $remainingStudents = $studentVehiclesIn - $studentVehiclesOut;
        $remainingStaff = $staffVehiclesIn - $staffVehiclesOut;
        $remainingGuests = $guestVehiclesIn - $guestVehiclesOut;

        return [
            Stat::make('Total Vehicles In', $totalVehiclesIn)
                ->icon('heroicon-o-truck')
                ->description("Students' Vehicles In: {$studentVehiclesIn}")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([1,2,3,4]),

            Stat::make('Total Vehicles Out', $totalVehiclesOut)
                ->icon('heroicon-o-truck')
                ->description("Students' Vehicles Out: {$studentVehiclesOut}")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('primary')
                ->chart([2,1]),

            // number of remaing vehicles in university calculated by subtracting the total vehicles in from the total vehicles out for each user type guest, staff, student
            Stat::make('Remaining Vehicles', $remainingStudents + $remainingStaff + $remainingGuests)
            ->icon('heroicon-o-truck')
            ->description("Students: {$remainingStudents}, Staff: {$remainingStaff}, Guests: {$remainingGuests}")
            ->descriptionIcon('heroicon-o-information-circle')
            ->color('primary')
            ->descriptionIcon('heroicon-o-light-bulb'),
                

        ];
    }
}
