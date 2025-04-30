<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Config;

class NumberPlateScanner extends Field
{
    protected string $view = 'forms.components.number-plate-scanner';

    public function getApiKey(): string
    {
        return Config::get('services.plate-recognition.api_key');
    }

    public function getApiUrl(): string
    {
        return Config::get('services.plate-recognition.api_url');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(function ($state) {
            // Clean or format the license plate if needed
            return $state;
        });
    }
}
