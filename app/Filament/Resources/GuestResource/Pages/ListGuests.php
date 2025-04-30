<?php

namespace App\Filament\Resources\GuestResource\Pages;

use App\Filament\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Builder;

class ListGuests extends ListRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Guest Name')
                ->searchable(),

            Tables\Columns\TextColumn::make('nic')
                ->label('NIC')
                ->sortable(),

            Tables\Columns\TextColumn::make('phone')
                ->label('Phone')
                ->sortable(),

            Tables\Columns\TextColumn::make('address')
                ->label('Address'),

                Tables\Columns\TextColumn::make('type')
                ->label('Vehicle Type')
                ->sortable(query: function ($query, $direction) {
                    return $query->orderBy('vehicles.type', $direction);
                }),
    
            Tables\Columns\TextColumn::make('license_plate')
                ->label('Vehicle License Plate')
                ->sortable(query: function ($query, $direction) {
                    return $query->orderBy('vehicles.license_plate', $direction);
                }),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Guest::query()
            ->join('vehicles', 'vehicles.owner_id', '=', 'guests.id')
            ->select('guests.*', 'vehicles.type', 'vehicles.license_plate') // very important!
            ->with('vehicles');
    }
}
