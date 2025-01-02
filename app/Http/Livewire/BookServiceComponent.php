<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\User;
use App\Models\Booking;

class BookServiceComponent extends Component
{
    public $category_id;
    public $provider_id;
    public $category;
    public $provider;
    public $services;
    public $selectedService;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $description = '';

    public function mount($category_id, $providerId = null)
    {
        $this->category_id = $category_id;
        $this->category = ServiceCategory::findOrFail($category_id);

        // If a specific provider is selected
        if ($providerId) {
            $this->provider_id = $providerId;
            $this->provider = User::findOrFail($providerId);
            
            // Load services for this provider in this category
            $this->services = Service::where('service_provider_id', $providerId)
                ->where('category_id', $category_id)
                ->where('is_active', true)
                ->get();
        }
    }

    public function selectService($serviceId)
    {
        $this->selectedService = Service::findOrFail($serviceId);
    }

    public function bookService()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'selectedService' => 'required|exists:services,id'
        ]);

        // Create booking
        $booking = Booking::create([
            'service_id' => $this->selectedService->id,
            'service_provider_id' => $this->selectedService->service_provider_id,
            'customer_id' => auth()->id(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'description' => $this->description,
            'status' => 'new'
        ]);

        // Flash success message
        session()->flash('message', 'Service booking submitted successfully for ' . $this->selectedService->name . '!');

        // Reset form after submission
        $this->reset(['name', 'email', 'phone', 'address', 'description', 'selectedService']);

        return redirect()->route('customer.dashboard');
    }

    public function render()
    {
        return view('livewire.book-service-component', [
            'category' => $this->category,
            'provider' => $this->provider ?? null,
            'services' => $this->services ?? []
        ])->layout('layouts.base');
    }
}
