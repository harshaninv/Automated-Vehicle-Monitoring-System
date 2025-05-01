<?php

namespace App\Filament\Resources\TimeLogResource\Pages;

use App\Filament\Resources\TimeLogResource;
use App\Forms\Components\NumberPlateScanner;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTimeLogs extends ListRecords
{
    protected static string $resource = TimeLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scan')
                ->icon('heroicon-o-camera')
                ->label('Scan Vehicle')
                ->form([
                    NumberPlateScanner::make('plate_number')
                        ->label('License Plate')
                        ->required(),
                    Select::make('action')
                        ->label('Action')
                        ->options([
                            'in' => 'Check In',
                            'out' => 'Check Out',
                        ])
                        ->required(),
                ])
                ->modalSubmitActionLabel('Add Log')
                ->action(function (array $data): void {
                    // Find the vehicle by license plate
                    $vehicle = \App\Models\Vehicle::where('license_plate', $data['plate_number'])->first();

                    if (!$vehicle) {
                        Notification::make()
                            ->title('Vehicle not found')
                            ->body('This vehicle is not registered in the system.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($data['action'] === 'in') {
                        // Check if vehicle already has an active log (time_in with no time_out)
                        $activeLog = $vehicle->timeLogs()->whereNull('time_out')->first();

                        if ($activeLog) {
                            Notification::make()
                                ->title('Vehicle already checked in')
                                ->body('This vehicle is already inside the premises.')
                                ->warning()
                                ->send();

                            return;
                        }

                        // Create new log with time_in
                        $vehicle->timeLogs()->create([
                            'time_in' => now(),
                        ]);

                        Notification::make()
                            ->title('Vehicle checked in')
                            ->body('The vehicle has been successfully checked in.')
                            ->success()
                            ->send();

                    } elseif ($data['action'] === 'out') {
                        // Find active log for this vehicle
                        $activeLog = $vehicle->timeLogs()->whereNull('time_out')->first();

                        if (!$activeLog) {
                            Notification::make()
                                ->title('No active check-in found')
                                ->body('This vehicle was not checked in or has already been checked out.')
                                ->warning()
                                ->send();

                            return;
                        }

                        // Update the log with time_out
                        $activeLog->update([
                            'time_out' => now(),
                        ]);

                        Notification::make()
                            ->title('Vehicle checked out')
                            ->body('The vehicle has been successfully checked out.')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
