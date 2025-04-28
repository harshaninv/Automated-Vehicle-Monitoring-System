<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VehicleType: String implements HasLabel
{
    case Car = 'car';
    case Van = 'van';
    case Motorcycle = 'motorcycle';
    case Truck = 'truck';
    case Bicycle = 'bicycle';
    case Other = 'other';

    public function getLabel(): string
    {
        return match($this) {
            self::Car => 'Car',
            self::Van => 'Van',
            self::Motorcycle => 'Motorcycle',
            self::Truck => 'Truck',
            self::Bicycle => 'Bicycle',
            self::Other => 'Other',
        };
    }
}