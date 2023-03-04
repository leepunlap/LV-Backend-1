<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'bookings_id',
        'users_id',
        'object',
        'amount',
        'currency',
        'created',
        'payment_method',
        'status',
        'details',
    ];
}
