<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_details_id',
        'passengers_id',
        'users_id',
        'date',
        'time',
        'flight_details',
        'status',
    ];

    public function billingDetails()
    {
        return $this->belongsTo(BillingDetail::class, 'billing_details_id');
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passengers_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'bookings_id');
    }
}
