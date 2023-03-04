<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AircraftImage extends Model
{
    use HasFactory;

    public $fillable = [
        'aircraft_id',
        'name',
        'uploaded_file',
        'status'
    ];
}
