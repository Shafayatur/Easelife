<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'service_provider_id', 
        'category_id', 
        'is_active'
    ];

    /**
     * Relationship with Service Provider (User)
     */
    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    /**
     * Relationship with Service Category
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Relationship with Bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Validate that a service is unique for a service provider and category
     */
    public static function validateUniqueService($serviceProviderId, $categoryId, $serviceName)
    {
        return !self::where('service_provider_id', $serviceProviderId)
            ->where('category_id', $categoryId)
            ->where('name', $serviceName)
            ->exists();
    }

    /**
     * Override the create method to enforce uniqueness
     */
    public static function createIfUnique($attributes)
    {
        if (self::validateUniqueService(
            $attributes['service_provider_id'], 
            $attributes['category_id'], 
            $attributes['name']
        )) {
            return self::create($attributes);
        }
        
        return null;
    }
}
