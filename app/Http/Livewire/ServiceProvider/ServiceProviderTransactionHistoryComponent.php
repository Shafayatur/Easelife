<?php

namespace App\Http\Livewire\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;

class ServiceProviderTransactionHistoryComponent extends Component
{
    use WithPagination;

    public $transactions = [];

    public function mount()
    {
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transactions = Payment::whereHas('booking', function($query) {
                $query->where('service_provider_id', Auth::id());
            })
            ->with(['booking.customer', 'booking.service'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.service-provider.service-provider-transaction-history-component')
            ->layout('layouts.base');
    }
}
