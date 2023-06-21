<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'airport_id',
        'aircraft_id',
        'charge_type_id',
        'title',
        'cost',
        'currency',
        'remarks',
        'created_by',
        'updated_by'
    ];

    public function charge_type()
    {
        return $this->belongsTo(ChargeType::class, 'charge_type_id', 'id');
    }

    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id', 'id');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id', 'id');
    }
}
