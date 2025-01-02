<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookServiceComponent extends Component
{
    public $categoryId;
    public $providerId;
    public $serviceId;
    public $address;
    public $city;
    public $postal_code;
    public $booking_date;
    public $booking_time;
    public $additional_description;
    public $requestSent = false;
    public $bookingMessage = null;
    public $existingPendingRequest = null;

    protected $rules = [
        'serviceId' => 'required|exists:services,id',
        'address' => 'required|string|max:255',
        'city' => 'required|string|max:100',
        'postal_code' => 'required|string|max:20',
        'booking_date' => 'required|date|after_or_equal:today',
        'booking_time' => 'required|date_format:H:i',
        'additional_description' => 'nullable|string|max:500'
    ];

    public function mount($categoryId, $providerId)
    {
        $this->categoryId = $categoryId;
        $this->providerId = $providerId;

        // Check for existing pending request when component loads
        $this->checkExistingPendingRequest();
    }

    public function checkExistingPendingRequest()
    {
        $this->existingPendingRequest = Booking::where('customer_id', Auth::id())
            ->where('service_provider_id', $this->providerId)
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_ACCEPTED
            ])
            ->first();

        if ($this->existingPendingRequest) {
            $this->bookingMessage = 'You have a pending booking request for this provider.';
        }
    }

    public function createBooking()
    {
        $this->validate();

        // Verify the service belongs to the provider and category
        $service = Service::findOrFail($this->serviceId);
        
        // Log the booking creation attempt
        \Log::info('Booking Creation Attempt', [
            'customer_id' => Auth::id(),
            'service_id' => $this->serviceId,
            'provider_id' => $this->providerId,
            'service_provider_id' => $service->service_provider_id
        ]);

        if ($service->service_provider_id != $this->providerId || $service->category_id != $this->categoryId) {
            Log::error('Invalid service selection', [
                'service_id' => $this->serviceId,
                'provider_id' => $this->providerId,
                'category_id' => $this->categoryId,
                'service_provider_id' => $service->service_provider_id,
                'service_category_id' => $service->category_id
            ]);
            $this->bookingMessage = 'Invalid service selection.';
            return;
        }

        // Check for existing pending or accepted bookings
        $existingBooking = Booking::where('customer_id', Auth::id())
            ->where('service_provider_id', $this->providerId)
            ->where('service_id', $this->serviceId)
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_ACCEPTED
            ])
            ->first();

        if ($existingBooking) {
            Log::warning('Existing booking found', [
                'existing_booking_id' => $existingBooking->id
            ]);
            $this->bookingMessage = 'You already have an active booking for this service with this provider.';
            $this->existingPendingRequest = $existingBooking;
            return;
        }

        try {
            // Create booking
            $booking = Booking::create([
                'customer_id' => Auth::id(),
                'service_provider_id' => $this->providerId,
                'service_id' => $this->serviceId,
                'status' => Booking::STATUS_PENDING,
                'date' => $this->booking_date,
                'time' => $this->booking_time,
                'total_price' => $service->price,
                'address' => $this->address,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'additional_description' => $this->additional_description
            ]);

            // Log the booking creation
            Log::info('Booking Created', [
                'booking_id' => $booking->id,
                'customer_id' => Auth::id(),
                'service_provider_id' => $this->providerId,
                'service_id' => $this->serviceId
            ]);

            // Create notification for service provider
            Notification::createBookingNotification($booking);

            // Set booking message and existing request
            $this->bookingMessage = 'Booking request sent successfully. The service provider will review your request.';
            $this->existingPendingRequest = $booking;

            // Reset form after successful submission
            $this->reset([
                'address', 
                'city', 
                'postal_code', 
                'booking_date', 
                'booking_time', 
                'additional_description'
            ]);
        } catch (\Exception $e) {
            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->bookingMessage = 'Failed to create booking request. Please try again.';
        }
    }

    public function render()
    {
        $services = Service::where('service_provider_id', $this->providerId)
            ->where('category_id', $this->categoryId)
            ->get();

        return view('livewire.customer.book-service-component', [
            'services' => $services,
            'bookingMessage' => $this->bookingMessage,
            'existingPendingRequest' => $this->existingPendingRequest
        ])->layout('layouts.base');
    }
}
