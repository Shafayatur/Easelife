<?php

namespace App\Http\Livewire\ServiceProvider;

use Livewire\Component;
use App\Models\Service;
use App\Models\Booking;
use App\Models\ServiceRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class ServiceProviderDashboardComponent extends Component
{
    public $activeSection = null;
    public $services = [];
    public $bookingCounts = [
        'new' => 0,
        'pending' => 0,
        'completed' => 0,
        'rejected' => 0
    ];
    public $newRequests = [];

    public function mount()
    {
        $this->loadBookingCounts();
        $this->loadNewRequests();
    }

    public function showMyServices()
    {
        $this->activeSection = 'my_services';
        $this->services = Service::where('service_provider_id', auth()->id())->get();
    }

    public function showBookings($status = null)
    {
        $query = ServiceRequest::where('service_provider_id', auth()->id());
        
        if ($status) {
            $query->where('status', $status);
        } else {
            // If no specific status, show pending and other active statuses
            $query->whereIn('status', [
                ServiceRequest::STATUS_PENDING, 
                ServiceRequest::STATUS_ACCEPTED, 
                ServiceRequest::STATUS_NEW
            ]);
        }
        
        return $query->with(['customer', 'service', 'payments'])->latest()->get();
    }

    protected function loadBookingCounts()
    {
        $providerId = auth()->id();

        $this->bookingCounts = [
            'new' => ServiceRequest::where('service_provider_id', $providerId)
                ->where('status', ServiceRequest::STATUS_PENDING)
                ->count(),
            'pending' => ServiceRequest::where('service_provider_id', $providerId)
                ->where('status', ServiceRequest::STATUS_ACCEPTED)
                ->count(),
            'completed' => ServiceRequest::where('service_provider_id', $providerId)
                ->where('status', ServiceRequest::STATUS_COMPLETED)
                ->count(),
            'rejected' => ServiceRequest::where('service_provider_id', $providerId)
                ->where('status', ServiceRequest::STATUS_REJECTED)
                ->count()
        ];
    }

    public function loadNewRequests()
    {
        $providerId = auth()->id();
        
        $this->newRequests = ServiceRequest::where('service_provider_id', $providerId)
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->with(['customer', 'service'])
            ->latest()
            ->get();
    }

    public function acceptRequest($requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);
        
        // Update request status
        $serviceRequest->update([
            'status' => ServiceRequest::STATUS_ACCEPTED
        ]);

        // Create a notification for the customer
        Notification::create([
            'user_id' => $serviceRequest->customer_id,
            'type' => 'service_request_accepted',
            'message' => 'Your service request for ' . $serviceRequest->service->name . ' has been accepted.',
            'data' => json_encode([
                'service_request_id' => $serviceRequest->id,
                'service_name' => $serviceRequest->service->name
            ])
        ]);

        // Refresh counts and new requests
        $this->loadBookingCounts();
        $this->loadNewRequests();

        session()->flash('message', 'Service request accepted successfully.');
    }

    public function rejectRequest($requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);
        
        // Update request status
        $serviceRequest->update([
            'status' => ServiceRequest::STATUS_REJECTED
        ]);

        // Create a notification for the customer
        Notification::create([
            'user_id' => $serviceRequest->customer_id,
            'type' => 'service_request_rejected',
            'message' => 'Your service request for ' . $serviceRequest->service->name . ' has been rejected.',
            'data' => json_encode([
                'service_request_id' => $serviceRequest->id,
                'service_name' => $serviceRequest->service->name
            ])
        ]);

        // Refresh counts and new requests
        $this->loadBookingCounts();
        $this->loadNewRequests();

        session()->flash('message', 'Service request rejected.');
    }

    public function markPaymentReceived($bookingId)
    {
        \Log::info('Marking payment received', [
            'booking_id' => $bookingId,
            'user_id' => auth()->id()
        ]);

        try {
            // Find the booking
            $booking = ServiceRequest::findOrFail($bookingId);
            
            \Log::info('Booking found', [
                'booking_id' => $booking->id,
                'service_provider_id' => $booking->service_provider_id,
                'current_user_id' => auth()->id()
            ]);

            // Ensure the booking belongs to the current service provider
            if ($booking->service_provider_id !== auth()->id()) {
                \Log::warning('Unauthorized payment marking attempt', [
                    'booking_id' => $bookingId,
                    'service_provider_id' => $booking->service_provider_id,
                    'current_user_id' => auth()->id()
                ]);
                session()->flash('error', 'Unauthorized action.');
                return;
            }

            // Find or create payment record
            $payment = $booking->payments()->firstOrNew([
                'booking_id' => $bookingId,
                'user_id' => $booking->customer_id
            ]);

            // Update payment status
            $payment->status = 'completed';
            $payment->payment_method = $payment->payment_method ?? 'manual';
            $payment->amount = $booking->total_price;
            $payment->save();

            \Log::info('Payment record updated', [
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount
            ]);

            // Create a notification for the customer
            Notification::create([
                'user_id' => $booking->customer_id,
                'type' => 'payment_confirmed',
                'message' => 'Payment for service ' . $booking->service->name . ' has been confirmed.',
                'data' => json_encode([
                    'booking_id' => $bookingId,
                    'service_name' => $booking->service->name,
                    'amount' => $booking->total_price
                ])
            ]);

            // Refresh the view
            $this->loadBookingCounts();

            session()->flash('message', 'Payment marked as received successfully.');
        } catch (\Exception $e) {
            \Log::error('Error marking payment received', [
                'booking_id' => $bookingId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to mark payment as received: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.service-provider.service-provider-dashboard-component', [
            'bookingCounts' => $this->bookingCounts,
            'services' => $this->services,
            'newRequests' => $this->newRequests
        ])->layout('layouts.base');
    }
}
