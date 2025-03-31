<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Register extends BaseRegister
{
    protected ?string $maxWidth = '4xl';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Profile')
                        ->icon('heroicon-o-user')
                        ->description('Tell us about yourself.')
                        ->schema([
                            $this->getNameFormComponent(),
                            Select::make('role')
                                ->label('Role')
                                ->live()
                                ->options([
                                    'student' => 'Student',
                                    'staff' => 'Staff',
                                ])
                                ->required()
                                ->default('student'),
                            TextInput::make('occupation')
                                ->label('Occupation')
                                ->placeholder('Occupation')
                                ->visible(fn(Get $get) => $get('role') === 'staff')
                                ->required(),
                            TextInput::make('uni_reg_no')
                                ->label('Student ID')
                                ->unique('students', 'uni_reg_no')
                                ->placeholder('University Registration Number')
                                ->visible(fn(Get $get) => $get('role') === 'student')
                                ->required(),
                            TextInput::make('nic')
                                ->label('NIC')
                                ->unique('users', 'nic')
                                ->placeholder('NIC')
                                ->required(),
                        ]),
                    Wizard\Step::make('Contact')
                        ->icon('heroicon-o-phone')
                        ->description('How can we reach you?')
                        ->schema([
                            TextInput::make('phone')
                                ->label('Phone')
                                ->placeholder('Phone')
                                ->required(),
                            TextInput::make('address')
                                ->label('Address')
                                ->placeholder('Address')
                                ->required(),
                        ]),
                    Wizard\Step::make('Account')
                        ->icon('heroicon-o-key')
                        ->description('Create your account.')
                        ->schema([
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="sm"
                        wire:submit="register"
                    >
                        Register
                    </x-filament::button>
                    BLADE
                ))),
            ]);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'nic' => $data['nic'],
        ]);

        // Assign the vehicle_owner role to the user
        $user->assignRole('vehicle_owner');

        // Create related student or staff record
        match ($data['role']) {
            'staff' => $user->staff()->create([
                'occupation' => $data['occupation'],
            ]),
            'student' => $user->student()->create([
                'uni_reg_no' => $data['uni_reg_no'],
            ]),
        };

        return $user;
    }

}
