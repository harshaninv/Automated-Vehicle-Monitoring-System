<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Staff;
use App\Models\Student;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        $data = $this->form->getState();

        // Update or create Staff or Student based on user_type
        if ($data['user_type'] === 'staff') {
            Staff::updateOrCreate(
                ['user_id' => $user->id],
                ['occupation' => $data['occupation'] ?? null]
            );
            // Optional: delete student record if switching type
            Student::where('user_id', $user->id)->delete();
        } elseif ($data['user_type'] === 'student') {
            Student::updateOrCreate(
                ['user_id' => $user->id],
                ['uni_reg_no' => $data['registration_number'] ?? null]
            );
            // Optional: delete staff record if switching type
            Staff::where('user_id', $user->id)->delete();
        }
    }
}
