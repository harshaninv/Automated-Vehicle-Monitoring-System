<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserInfoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //display the user's details in the dashboard
            Stat::make('Account details', auth()->user()->name)
                ->icon('heroicon-o-user')
                // in the description, display the user's role and detilas of the role like if staff, display the occupation and if student, display the student ID
                // ->description( auth()->user()->role === 'staff' ? 'You signed as a '.auth()->user()->role . ' member and you are the ' . auth()->user()->staff->occupation : 'You signed as a '.auth()->user()->role . ' and you are ' . auth()->user()->student->uni_reg_no)
                ->description( auth()->user()->role === 'staff' ? 'You signed as a '.auth()->user()->role . ' member and you are the ' . auth()->user()->name : 'You signed as a '.auth()->user()->role . ' and you are ' . auth()->user()->name)
                ->descriptionIcon('heroicon-o-information-circle')
        ];
    }

}
