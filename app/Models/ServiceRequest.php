<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    // Fillable fields
    protected $fillable = [
        'customer_id',
        'service_provider_id',
        'service_id',
        'status',
        'details',
        'scheduled_date',
        'address',
        'city',
        'postal_code'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'id');
    }

    // Scope for filtering by status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Mutator for scheduled date
    public function setScheduledDateAttribute($value)
    {
        $this->attributes['scheduled_date'] = \Carbon\Carbon::parse($value);
    }

    // Accessor for scheduled date
    public function getScheduledDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value);
    }
}
