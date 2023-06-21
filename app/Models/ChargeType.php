<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_id',
        'created_by',
        'updated_by'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
