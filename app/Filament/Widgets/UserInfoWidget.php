<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Enums\UserStatus;

class UserInfoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $status = $user->status;

        // Get user's vehicles
        $vehicles = $user->vehicles()->get();

        // Format vehicle list for description with type and license plate
        $vehicleList = $vehicles->map(function ($vehicle) {
            return $vehicle->type->value . ' - ' . $vehicle->license_plate;
        })->implode(', ');

        // Status text based on enum value
        $statusText = match ($status) {
            UserStatus::APPROVED => "✅ Vehicle access approved. Vehicles: $vehicleList",
            UserStatus::REJECTED => "❌ Vehicle access rejected.",
            UserStatus::PENDING => "⏳ Vehicle access request is pending.",
            default => "Vehicle access status unknown.",
        };

        return [
            Stat::make('Account details', $user->name)
                ->icon('heroicon-o-user')
                ->description(
                    ($user->role === 'staff'
                        ? 'You signed as a ' . $user->role . ' member and you are the ' . $user->name
                        : 'You signed as a ' . $user->role . ' and you are ' . $user->name
                    ) . "\n" . $statusText
                )
                ->descriptionIcon('heroicon-o-information-circle'),
        ];
    }
}
