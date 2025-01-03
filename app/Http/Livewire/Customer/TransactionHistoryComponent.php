<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class TransactionHistoryComponent extends Component
{
    use WithPagination;

    public $transactions = [];

    public function mount()
    {
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transactions = Payment::where('user_id', Auth::id())
            ->with(['booking.service', 'booking.serviceProvider'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.transaction-history-component')
            ->layout('layouts.base');
    }
}
