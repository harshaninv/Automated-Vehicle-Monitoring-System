<?php

namespace App\Filament\Resources\GuestResource\Pages;

use App\Filament\Resources\GuestResource;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Resources\Pages\CreateRecord;

class CreateGuest extends CreateRecord
{
    protected static string $resource = GuestResource::class;

    protected array $vehicleData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if NIC exists in the users table
        if (User::where('nic', $data['nic'])->exists()) {
            throw \Filament\Notifications\Notification::make()
                ->title('NIC already exists in users.')
                ->danger()
                ->send();
            abort(403, 'NIC already exists in users.');
        }

        // Check if NIC exists in guests table
        if (\App\Models\Guest::where('nic', $data['nic'])->exists()) {
            throw \Filament\Notifications\Notification::make()
                ->title('NIC already exists in guests.')
                ->danger()
                ->send();
            abort(403, 'NIC already exists in guests.');
        }

        // Save vehicle info separately (we'll create vehicle after guest is created)
        $this->vehicleData = [
            'type' => $data['type'],
            'license_plate' => $data['license_plate'],
        ];

        // Remove vehicle fields from $data (because Guest table does not have them)
        unset($data['type'], $data['license_plate']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->vehicleData['type']) && !empty($this->vehicleData['license_plate'])) {
            Vehicle::create([
                'owner_type' => get_class($this->record), // App\Models\Guest
                'owner_id' => $this->record->id,
                'type' => $this->vehicleData['type'],
                'license_plate' => $this->vehicleData['license_plate'],
            ]);
        }
    }
}
