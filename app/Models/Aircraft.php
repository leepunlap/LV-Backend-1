<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Aircraft extends Model
{
    use HasFactory;

    public $fillable = [
        'operator_id',
        'model',
        'manufacture',
        'type',
        'icao',
        'mtow',
        'lv_margin',
        'aircraft_length',
        'cabin_length',
        'cabin_height',
        'aircraft_width',
        'cabin_width',
        'max_speed',
        'max_range',
        'max_altitude',
        'fuel_burn_per_hour',
        'capacity',
        'pax',
        'no_of_crew',
        'hourly_rate',
        'crew_per_diem',
        'crew_hotel',
        'total_crew_cost',
        'margin',
        'wifi',
        'full_size_bathrooms',
        'meal_service',
        'pet_accomodation',
        'wide_screen_televisions',
        'ambient_lighting',
        'cabin_crew'
    ];

    public function images()
    {
        return $this->hasMany(AircraftImage::class, 'aircraft_id');
    }

    public function prevImages()
    {
        return $this->hasMany(AircraftImage::class, 'aircraft_id');
    }

    public function type()
    {
        return $this->belongsTo(AircraftType::class, 'type', 'id');
    }

    public function manufacture()
    {
        return $this->belongsTo(AircraftManufacture::class, 'manufacture', 'id');
    }

    public function amenities()
    {
        return $this->hasMany(AircraftAmenity::class, 'aircraft_id', 'id');
    }

    public function charges()
    {
        return $this->hasMany(AircraftCharge::class, 'aircrafts_id');
    }
}
