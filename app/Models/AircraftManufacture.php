<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AircraftManufacture extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'status',
        'users_id'
    ];
}
