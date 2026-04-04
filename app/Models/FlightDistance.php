<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightDistance extends Model
{
    protected $fillable = [
        'origin_airport_id',
        'destination_airport_id',
        'distance_km',
        'bearing_deg',
    ];

    protected $casts = [
        'distance_km' => 'float',
        'bearing_deg' => 'float',
    ];

    public function originAirport()
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    public function destinationAirport()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }
}
