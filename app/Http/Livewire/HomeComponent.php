<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ServiceCategory;

class HomeComponent extends Component
{
    public function render()
    {
        $categories = ServiceCategory::all();
        return view('livewire.home-component', compact('categories'))->layout('layouts.base');
    }
}
