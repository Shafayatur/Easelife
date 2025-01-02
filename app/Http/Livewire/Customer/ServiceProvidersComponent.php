<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;

class ServiceProvidersComponent extends Component
{
    public $categoryId;
    public $serviceProviders = [];
    public $selectedCategory = null;

    public function mount($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->loadServiceProviders();
    }

    public function loadServiceProviders()
    {
        // Find the category
        $this->selectedCategory = ServiceCategory::findOrFail($this->categoryId);

        // Find service providers who offer this category of service
        $this->serviceProviders = User::whereHas('services', function($query) {
            $query->where('category_id', $this->categoryId)
                  ->where('is_active', true);
        })->with(['services' => function($query) {
            $query->where('category_id', $this->categoryId)
                  ->where('is_active', true);
        }])->get();
    }

    public function bookService($providerId)
    {
        // Redirect to booking page for this specific provider and category
        return redirect()->route('customer.book_service', [
            'categoryId' => $this->categoryId, 
            'providerId' => $providerId
        ]);
    }

    public function render()
    {
        return view('livewire.customer.service-providers-component', [
            'serviceProviders' => $this->serviceProviders,
            'category' => $this->selectedCategory
        ])->layout('layouts.base');
    }
}
