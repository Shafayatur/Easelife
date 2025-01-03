<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;

class CustomerProfileComponent extends Component
{
    public $user;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('livewire.customer.customer-profile-component')
            ->layout('layouts.base');
    }
}
