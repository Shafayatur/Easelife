<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    const TYPE_SERVICE_REQUEST = 'service_request';
    const TYPE_SERVICE_REQUEST_ACCEPTED = 'service_request_accepted';
    const TYPE_SERVICE_REQUEST_REJECTED = 'service_request_rejected';

    const TYPE_BOOKING_REQUEST = 'booking_request';
    const TYPE_BOOKING_ACCEPTED = 'booking_accepted';
    const TYPE_BOOKING_REJECTED = 'booking_rejected';
    const TYPE_BOOKING_COMPLETED = 'booking_completed';

    const STATUS_UNREAD = 'unread';
    const STATUS_READ = 'read';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'related_model_type',
        'related_model_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relatedModel()
    {
        return $this->morphTo();
    }

    public static function createServiceRequestNotification(ServiceRequest $serviceRequest)
    {
        return self::create([
            'user_id' => $serviceRequest->service_provider_id,
            'type' => self::TYPE_SERVICE_REQUEST,
            'message' => "New service request from {$serviceRequest->customer->name} for {$serviceRequest->service->name}",
            'related_model_type' => ServiceRequest::class,
            'related_model_id' => $serviceRequest->id,
            'status' => self::STATUS_UNREAD
        ]);
    }

    public static function createBookingNotification(Booking $booking)
    {
        // Log the details of the booking notification creation
        \Log::info('Creating Booking Notification', [
            'booking_id' => $booking->id,
            'service_provider_id' => $booking->service_provider_id,
            'customer_name' => $booking->customer->name ?? 'Unknown',
            'service_name' => $booking->service->name ?? 'Unknown'
        ]);

        return self::create([
            'user_id' => $booking->service_provider_id,
            'type' => self::TYPE_BOOKING_REQUEST,
            'message' => "New booking request from {$booking->customer->name} for {$booking->service->name}",
            'related_model_type' => Booking::class,
            'related_model_id' => $booking->id,
            'status' => self::STATUS_UNREAD
        ]);
    }

    public static function createBookingStatusNotification(Booking $booking)
    {
        // Log the details of the booking status notification creation
        \Log::info('Creating Booking Status Notification', [
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'status' => $booking->status,
            'service_name' => $booking->service->name ?? 'Unknown',
            'service_provider_name' => $booking->serviceProvider->name ?? 'Unknown'
        ]);

        $type = match($booking->status) {
            Booking::STATUS_ACCEPTED => self::TYPE_BOOKING_ACCEPTED,
            Booking::STATUS_REJECTED => self::TYPE_BOOKING_REJECTED,
            default => null
        };

        if (!$type) return null;

        $message = match($booking->status) {
            Booking::STATUS_ACCEPTED => "Your booking for {$booking->service->name} has been accepted by {$booking->serviceProvider->name}",
            Booking::STATUS_REJECTED => "Your booking for {$booking->service->name} has been rejected by {$booking->serviceProvider->name}",
            default => null
        };

        return self::create([
            'user_id' => $booking->customer_id,
            'type' => $type,
            'message' => $message,
            'related_model_type' => Booking::class,
            'related_model_id' => $booking->id,
            'status' => self::STATUS_UNREAD
        ]);
    }
}
