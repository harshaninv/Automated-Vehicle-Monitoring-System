<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeLogResource\Pages;
use App\Filament\Resources\TimeLogResource\RelationManagers;
use App\Forms\Components\NumberPlateScanner;
use App\Models\TimeLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimeLogResource extends Resource
{
    protected static ?string $model = TimeLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plate_number')
                    ->label('Vehicle License Plate')
                    ->relationship('vehicle', 'license_plate')
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_in')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_out')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\Action::make('time-log')
                    ->icon('heroicon-o-clock')
                    ->form([
                        Forms\Components\Section::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2
                            ])
                            ->schema([
                                Forms\Components\DateTimePicker::make('time_in')
                                    ->default(fn(TimeLog $record) => $record->time_in ?? now())
                                    ->required(),
                                Forms\Components\DateTimePicker::make('time_out')
                                    ->default(fn($record) => $record->time_out ?? now())
                                    ->required(),
                            ]),
                    ])
                    ->action(fn(TimeLog $record, array $data) => $record->update([
                        'time_in' => $data['time_in'],
                        'time_out' => $data['time_out'],
                    ])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('vehicle_owner')) {
            return parent::getEloquentQuery()
                ->whereHas('vehicle', function ($query) {
                    $query->where('owner_id', auth()->user()->id);
                });
        }
        return parent::getEloquentQuery()->withoutGlobalScopes();
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
            'index' => Pages\ListTimeLogs::route('/'),
            'create' => Pages\CreateTimeLog::route('/create'),
            'edit' => Pages\EditTimeLog::route('/{record}/edit'),
        ];
    }
}
