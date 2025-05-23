<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestResource\Pages;
use App\Models\Guest;
use App\Enums\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\RepeaterColumn;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->steps([
                        Step::make('Guest Information')
                            ->icon('heroicon-o-user')
                            ->description('Enter guest personal details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Full Name'),

                                Forms\Components\TextInput::make('nic')
                                    ->required()
                                    ->label('NIC Number')
                                    ->rule(function (Forms\Get $get, ?Guest $record) {
                                        return function (string $attribute, $value, $fail) use ($record) {
                                            $queryGuest = \App\Models\Guest::where('nic', $value);
                                            $queryUser = \App\Models\User::where('nic', $value);

                                            if ($record) {
                                                $queryGuest->where('id', '!=', $record->id);
                                            }

                                            if ($queryGuest->exists() || $queryUser->exists()) {
                                                $fail('This NIC already exists.');
                                            }
                                        };
                                    }),

                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->label('Phone Number')
                                    ->rule(function (Forms\Get $get, ?Guest $record) {
                                        return function (string $attribute, $value, $fail) use ($record) {
                                            $query = \App\Models\Guest::where('phone', $value);

                                            if ($record) {
                                                $query->where('id', '!=', $record->id);
                                            }

                                            if ($query->exists()) {
                                                $fail('This Phone Number already exists.');
                                            }
                                        };
                                    }),

                                Forms\Components\TextInput::make('address')
                                    ->required()
                                    ->label('Address'),
                            ]),

                        Step::make('Vehicle Information')
                            ->icon('heroicon-o-truck')
                            ->description('Enter guest vehicle details')
                            ->schema(function (?Guest $record) {
                                if ($record) {
                                    // Editing: show Repeater
                                    return [
                                        Forms\Components\Repeater::make('vehicles')
                                            ->relationship('vehicles')
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->options(
                                                        collect(VehicleType::cases())
                                                            ->mapWithKeys(fn($enum) => [$enum->value => $enum->getLabel()])
                                                            ->toArray()
                                                    )
                                                    ->required()
                                                    ->label('Vehicle Type')
                                                    ->placeholder('Select an option'),

                                                Forms\Components\TextInput::make('license_plate')
                                                    ->regex('/^([A-Z]{1,2})\s([A-Z]{1,3})\s([0-9]{4}(?<!0{4}))/')
                                                    ->unique(
                                                        table: 'vehicles',
                                                        column: 'license_plate',
                                                        ignoreRecord: true,
                                                    )
                                                    ->placeholder('WP ABC XXXX')
                                                    ->required(),
                                            ])
                                            ->label('Vehicles'),
                                    ];
                                } else {
                                    // Creating: show single vehicle fields
                                    return [
                                        Forms\Components\Group::make([
                                            Forms\Components\Select::make('type')
                                                ->options(
                                                    collect(VehicleType::cases())
                                                        ->mapWithKeys(fn($enum) => [$enum->value => $enum->getLabel()])
                                                        ->toArray()
                                                )
                                                ->required()
                                                ->label('Vehicle Type')
                                                ->placeholder('Select an option'),

                                            Forms\Components\TextInput::make('license_plate')
                                                ->regex('/^([A-Z]{1,2})\s([A-Z]{1,3})\s([0-9]{4}(?<!0{4}))/')
                                                ->unique(
                                                    table: 'vehicles',
                                                    column: 'license_plate',
                                                    ignoreRecord: true,
                                                )
                                                ->placeholder('WP ABC XXXX')
                                                ->required(),
                                        ])
                                        ->label('Vehicles'),
                                    ];
                                }
                            }),
                    ])
                    ->columnSpan('full')
                    ->columns(1)
                    ->maxWidth('7xl'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nic')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicles')
                    ->label('Vehicles')
                    ->formatStateUsing(function ($record) {
                        return $record->vehicles
                            ->map(fn ($v) => "{$v->type->getLabel()} ({$v->license_plate})")
                            ->implode('<br>');
                    })
                    ->html()
                    ->wrap(),
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
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
}
