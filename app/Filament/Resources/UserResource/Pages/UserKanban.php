<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use App\Enums\UserStatus;
use Filament\Pages\Page;

class UserKanban extends Page
{
    protected static string $resource = \App\Filament\Resources\UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.kanban';

    public function getUsersByStatus(): array
    {
        return [
            UserStatus::PENDING->value => User::where('status', UserStatus::PENDING)->get(),
            UserStatus::APPROVED->value => User::where('status', UserStatus::APPROVED)->get(),
            UserStatus::REJECTED->value => User::where('status', UserStatus::REJECTED)->get(),
        ];
    }
}
