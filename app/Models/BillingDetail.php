<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'passengers_id',
        'card_name',
        'country',
        'city',
        'street1',
        'street2',
        'zipcode',
    ];
}
