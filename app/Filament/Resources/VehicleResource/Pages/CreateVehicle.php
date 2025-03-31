<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use App\Models\Guest;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            'owner_id' => $data['owner_id'],
            'owner_type' => $data['owner_type'] === 'user' ? User::class : Guest::class,
            'type' => $data['type'],
            'license_plate' => $data['license_plate'],
        ];
    }
}
