<?php

namespace App\Filament\Resources;

use App\Enums\VehicleType;
use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Guest;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('owner_type')
                    ->label('Owner Type')
                    ->options([
                        'user' => 'Registered User',
                        'guest' => 'Guest',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\Select::make('owner_id')
                    ->label('Select Owner')
                    ->options(function (Get $get) {
                        $type = $get('owner_type');

                        return match ($type) {
                            'user' => User::pluck('name', 'id'),
                            'guest' => Guest::pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('type')
                    ->options(VehicleType::class)
                    ->required(),

                Forms\Components\TextInput::make('license_plate')
                    ->regex('/^([A-Z]{1,2})\s([A-Z]{1,3})\s([0-9]{4}(?<!0{4}))/')
                    ->unique('vehicles', 'license_plate')
                    ->placeholder('WP ABC XXXX')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('owner.name'),
                    Tables\Columns\TextColumn::make('license_plate')
                        ->weight(FontWeight::Bold)
                        ->searchable(),
                    Tables\Columns\TextColumn::make('type')
                        ->color('gray'),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
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
public static function getEloquentQuery(): Builder
{
    if (auth()->user()->hasRole('vehicle_owner')) {
        return parent::getEloquentQuery()
            ->where('owner_id', auth()->user()->id);
    }
    return parent::getEloquentQuery()->withoutGlobalScopes();
}



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
