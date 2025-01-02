<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;
use App\Models\ServiceRequest;
use App\Models\Booking;

class CustomerDashboardComponent extends Component
{
    public $serviceRequests = [];
    public $confirmedServices = [];

    public function mount()
    {
        $this->loadServiceRequests();
        $this->loadConfirmedServices();
    }

    public function loadServiceRequests()
    {
        $this->serviceRequests = ServiceRequest::where('customer_id', auth()->id())
            ->whereIn('status', [
                ServiceRequest::STATUS_PENDING, 
                ServiceRequest::STATUS_ACCEPTED
            ])
            ->with(['serviceProvider', 'service'])
            ->latest()
            ->get();
    }

    public function loadConfirmedServices()
    {
        $this->confirmedServices = Booking::where('customer_id', auth()->id())
            ->whereIn('status', [
                Booking::STATUS_ACCEPTED, 
                Booking::STATUS_COMPLETED
            ])
            ->with(['serviceProvider', 'service', 'payments'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.customer-dashboard-component', [
            'serviceRequests' => $this->serviceRequests,
            'confirmedServices' => $this->confirmedServices
        ])->layout('layouts.base');
    }
}
