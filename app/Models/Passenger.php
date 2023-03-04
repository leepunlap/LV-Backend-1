<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'first_name',
        'last_name',
        'title',
        'email',
        'phone',
        'passport_number',
        'country_of_issue',
        'expiry_date',
        'concierge',
        'request',
        'have_pet',
        'type_of_pet'
    ];
}
