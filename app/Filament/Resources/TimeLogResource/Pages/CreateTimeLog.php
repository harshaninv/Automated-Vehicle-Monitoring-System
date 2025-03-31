<?php

namespace App\Filament\Resources\TimeLogResource\Pages;

use App\Filament\Resources\TimeLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeLog extends CreateRecord
{
    protected static string $resource = TimeLogResource::class;
}
