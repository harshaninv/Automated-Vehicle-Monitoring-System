<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vehicle extends Model
{
    protected $fillable = [
        'owner_id', 'owner_type', 'type', 'license_plate'
    ];

    protected $casts = [
        'type' => VehicleType::class,
    ];

    public function reasons(): HasMany
    {
        return $this->hasMany(Reason::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

//    public function getOwnerAttribute()
//    {
//        return $this->owner_type::find($this->owner_id);
//    }

}
