<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        $user = $this->record;
        $data = $this->form->getState();

        // Fetch the vehicle_owner role ID
        $vehicleOwnerRoleId = Role::where('name', 'vehicle_owner')->first()?->id;

        // If the user has the vehicle_owner role, create a student or staff record
        if ($vehicleOwnerRoleId) {
            match ($data['vehicle_owner_type']) {
                'staff' => $user->staff()->update([
                    'occupation' => $data['occupation'],
                ]),
                'student' => $user->student()->update([
                    'uni_reg_no' => $data['uni_reg_no'],
                ]),
            };
        }
    }
}
