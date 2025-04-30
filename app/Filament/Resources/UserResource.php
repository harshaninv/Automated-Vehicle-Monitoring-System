<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Enums\UserStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    use IsKanbanStatus;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 5;

    // Add Kanban Configuration
    protected static string $statusEnum = UserStatus::class;
    protected static string $statusColumn = 'status';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone')->tel()->required(),
                Forms\Components\TextInput::make('address')->required(),
                Forms\Components\TextInput::make('nic')->required(),
                Forms\Components\TextInput::make('password')->password()->required()->visibleOn('create'),
                Forms\Components\Select::make('roles')->relationship('roles', 'name')->preload()->required()->multiple()->live()
                    ->options(Role::all()->pluck('name', 'id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('address')->searchable(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'kanban' => Pages\UserKanban::route('/kanban'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // Add Kanban board functionality
    public static function kanban(): array
    {
        return [
            'status' => 'status', // Bind status field for Kanban
        ];
    }

    // Optional: Add additional methods for drag-and-drop behavior
    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        User::find($recordId)->update(['status' => $status]);
    }

    public function onSortChanged(int $recordId, string $status, array $orderedIds): void
    {
        // Add custom sorting behavior if necessary
    }
}
