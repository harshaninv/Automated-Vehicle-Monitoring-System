<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Staff;
use App\Models\Student;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;
        $data = $this->form->getState();

        // Create Staff or Student based on selected user_type
        if ($data['user_type'] === 'staff') {
            Staff::create([
                'user_id' => $user->id,
                'occupation' => $data['occupation'] ?? null,
            ]);
        } elseif ($data['user_type'] === 'student') {
            Student::create([
                'user_id' => $user->id,
                'uni_reg_no' => $data['registration_number'] ?? null,
            ]);
        }
    }
}
