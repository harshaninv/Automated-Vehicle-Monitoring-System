<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        // Fetch the vehicle_owner role ID
        $vehicleOwnerRoleId = Role::where('name', 'vehicle_owner')->first()?->id;

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->required(),
                Forms\Components\TextInput::make('nic')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->visibleOn('create'),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()
                    ->multiple()
                    ->live()
                    ->options(Role::all()->pluck('name', 'id')), // Use 'id' as the value, 'name' as the label
                    // ->disabled(fn(Get $get) => $vehicleOwnerRoleId && in_array((string) $vehicleOwnerRoleId, $get('roles') ?? [])),

                // Show student/staff selection if vehicle_owner is selected
                Forms\Components\Select::make('vehicle_owner_type')
                    ->label('Vehicle Owner Type')
                    ->options([
                        'student' => 'Student',
                        'staff' => 'Staff',
                    ])
                    ->live()
                    ->required()
                    ->visible(fn(Get $get) => $vehicleOwnerRoleId && in_array((string) $vehicleOwnerRoleId, $get('roles') ?? [])),

                // Show student ID input if vehicle_owner_type is student
                Forms\Components\TextInput::make('uni_reg_no')
                    ->label('Student ID')
                    ->unique('students', 'uni_reg_no')
                    ->placeholder('University Registration Number')
                    ->visible(fn(Get $get) => $vehicleOwnerRoleId && in_array((string) $vehicleOwnerRoleId, $get('roles') ?? []) && $get('vehicle_owner_type') === 'student')
                    ->required(fn(Get $get) => $get('vehicle_owner_type') === 'student'),

                // Show occupation input if vehicle_owner_type is staff
                Forms\Components\TextInput::make('occupation')
                    ->label('Occupation')
                    ->placeholder('Occupation')
                    ->visible(fn(Get $get) => $vehicleOwnerRoleId && in_array((string) $vehicleOwnerRoleId, $get('roles') ?? []) && $get('vehicle_owner_type') === 'staff')
                    ->required(fn(Get $get) => $get('vehicle_owner_type') === 'staff'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nic')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
