<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;
        $data = $this->form->getState();

        // Fetch the vehicle_owner role ID
        $vehicleOwnerRoleId = Role::where('name', 'vehicle_owner')->first()?->id;
        

        // If the user has the vehicle_owner role, create a student or staff record
        if ($vehicleOwnerRoleId) {
            // dd($data['vehicle_owner_type']);
            match ($data['vehicle_owner_type']) {
                'staff' => $user->staff()->create([
                    'occupation' => $data['occupation'],
                ]),
                'student' => $user->student()->create([
                    'uni_reg_no' => $data['uni_reg_no'],
                ]),
            };
        }
    }
}
