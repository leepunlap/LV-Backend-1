<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalService extends Model
{
    use HasFactory;

    protected $table = 'additional_services';

    protected $fillable = [
        'bookings_id',
        'category_code',
        'description',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookings_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_code', 'code');
    }
}