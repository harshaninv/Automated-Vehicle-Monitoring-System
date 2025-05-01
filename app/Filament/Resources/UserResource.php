<?php

namespace App\Filament\Resources;

use App\Enums\UserStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required(),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('phone')
                ->tel()
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('address')
                ->required(),

            Forms\Components\TextInput::make('nic')
                ->required()
                ->unique(ignoreRecord: true),

            // Show password only on create
            Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->visible(fn ($get, $record) => $record === null),

            // Password Confirmation for extra security on creation
            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->required()
                ->visible(fn ($get, $record) => $record === null),

            // Roles
            Forms\Components\Select::make('roles')
                ->relationship('roles', 'name')
                ->preload()
                ->required()
                ->multiple()
                ->options(Role::all()->pluck('name', 'id')),

            // Status
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    UserStatus::PENDING->value => 'Pending',
                    UserStatus::APPROVED->value => 'Approved',
                    UserStatus::REJECTED->value => 'Rejected',
                ])
                ->required(),
            
                Forms\Components\Select::make('user_type')
                ->label('User Type')
                ->required()
                ->options([
                    'student' => 'Student',
                    'staff' => 'Staff',
                ])
                ->reactive()
                ->visible(fn ($record) => $record === null) // only visible on create
                ->afterStateUpdated(function ($state, $set) {
                    $set('occupation', null);
                    $set('registration_number', null);
                }),
            
            // Hidden/disabled field for user_type on edit (auto-detected)
            Forms\Components\TextInput::make('user_type')
                ->label('User Type')
                ->visible(fn ($record) => $record !== null) // only on edit
                ->afterStateHydrated(function ($component, $record) {
                    if ($record) {
                        $user_id = $record->id;
                        if (\App\Models\Student::where('user_id', $user_id)->exists()) {
                            $component->state('Student');
                        } elseif (\App\Models\Staff::where('user_id', $user_id)->exists()) {
                            $component->state('Staff');
                        } else {
                            $component->state('Unknown');
                        }
                    }
                }),
            
            // Occupation (for staff only)
            Forms\Components\TextInput::make('occupation')
            ->label('Occupation')
            ->required(fn ($get) => $get('user_type') === 'staff')
            ->visible(function ($get, $record) {
                return $get('user_type') === 'staff' ||
                ($record && \App\Models\Staff::where('user_id', $record->id)->exists());
            })
            ->reactive()
            ->afterStateHydrated(function ($component, $record) {
                if ($record) {
                $occupation = \Illuminate\Support\Facades\DB::table('staff')
                    ->where('user_id', $record->id)
                    ->value('occupation');
                $component->state($occupation);
                }
            }),
            
            // Registration No (for student only)
            Forms\Components\TextInput::make('registration_number')
                ->label('Registration No')
                ->required(fn ($get) => $get('user_type') === 'student')
                ->visible(function ($get, $record) {
                    return $get('user_type') === 'student' ||
                    ($record && \App\Models\Student::where('user_id', $record->id)->exists());
                })
                ->reactive()
                ->afterStateHydrated(function ($component, $record) {
                if ($record) {
                $regNo = \Illuminate\Support\Facades\DB::table('students')
                    ->where('user_id', $record->id)
                    ->value('uni_reg_no');
                $component->state($regNo);
                }
            }),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable(),

                    Tables\Columns\TextColumn::make('email')
                        ->color('gray')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Registered At')
                        ->dateTime()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->formatStateUsing(fn (UserStatus $state) => $state->label())
                        ->color(fn (UserStatus $state): string => match ($state) {
                            UserStatus::APPROVED => 'success',
                            UserStatus::PENDING => 'warning',
                            UserStatus::REJECTED => 'danger',
                            default => 'gray',
                        }),
                ]),

            ])
            ->contentGrid(['md' => 2, 'xl' => 3])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        UserStatus::PENDING->value => 'Pending',
                        UserStatus::APPROVED->value => 'Approved',
                        UserStatus::REJECTED->value => 'Rejected',
                    ])
                    ->label('Filter by Status'),
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
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('user')) {
            return parent::getEloquentQuery()
                ->where('id', auth()->user()->id);
        }

        return parent::getEloquentQuery()->withoutGlobalScopes();
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
