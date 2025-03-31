<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Guest extends Model
{
    protected $fillable = [
        'name', 'nic', 'phone', 'address'
    ];

    public function vehicles(): MorphMany
    {
        return $this->morphMany(Vehicle::class, 'owner');
    }
}
