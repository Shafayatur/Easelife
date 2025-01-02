<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'service_provider_id',
        'service_id',
        'status',
        'date',
        'time',
        'total_price',
        'address',
        'city',
        'postal_code',
        'additional_description',
        'rejection_reason'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'total_price' => 'decimal:2'
    ];

    /**
     * The status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relationship with Service Provider
     */
    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    /**
     * Relationship with Service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relationship with Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Completed',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get the status color class
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACCEPTED => 'primary',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_COMPLETED => 'success',
            default => 'secondary'
        };
    }
}
