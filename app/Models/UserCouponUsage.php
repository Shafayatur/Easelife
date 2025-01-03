<?php

namespace App\Models;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_code',
        'booking_id',
        'discount_amount'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
