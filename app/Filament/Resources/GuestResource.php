<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestResource\Pages;
use App\Models\Guest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Import Wizard
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
                                    ->rule(function () {
                                        return function (string $attribute, $value, $fail) {
                                            if (\App\Models\User::where('nic', $value)->exists()) {
                                                $fail('This NIC already exists.');
                                            }
                                            if (\App\Models\Guest::where('nic', $value)->exists()) {
                                                $fail('This NIC already exists.');
                                            }
                                        };
                                    }),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->label('Phone Number')
                                    ->rule(function () {
                                        return function (string $attribute, $value, $fail) {
                                            if (\App\Models\Guest::where('phone', $value)->exists()) {
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
                            ->schema([
                                Forms\Components\TextInput::make('vehicle_type')
                                    ->label('Vehicle Type')
                                    ->required()
                                    ->placeholder('car, bike, van, etc.'),
                                Forms\Components\TextInput::make('license_plate')
                                    ->label('License Plate Number')
                                    ->required()
                                    ->placeholder('e.g., ABC-1234'),
                            ]),
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
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('Vehicle Type')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('license_plate')
                    ->label('License Plate')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
}
