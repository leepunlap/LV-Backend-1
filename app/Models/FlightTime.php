<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightTime extends Model
{
    use HasFactory;

    protected $fillable = [
        "aircraft_id",
        "origin_id",
        "destination_id",
        "flight_time",
        "created_by",
        "updated_by",
    ];
}
