<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

class AdminDashboardComponent extends Component
{
    public $totalCustomers;
    public $totalServiceProviders;
    public $totalServiceCategories;
    public $pendingNidVerifications;
    public $verifiedNidVerifications;

    public function mount()
    {
        // Debug logging to understand user counts
        \Log::info('User Count Debug', [
            'total_customers' => User::where('user_type', 'customer')->count(),
            'total_service_providers' => User::where('user_type', 'sp')->count(),
            'all_users' => User::groupBy('user_type')
                ->select('user_type', DB::raw('count(*) as count'))
                ->get()
                ->toArray()
        ]);

        // Ensure these are calculated correctly
        $this->totalCustomers = User::where('user_type', 'customer')->count();
        $this->totalServiceProviders = User::where('user_type', 'sp')->count();
        $this->totalServiceCategories = ServiceCategory::count();
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard-component', [
            'totalCustomers' => $this->totalCustomers,
            'totalServiceProviders' => $this->totalServiceProviders,
            'totalServiceCategories' => $this->totalServiceCategories,
            'pendingNidVerifications' => $this->pendingNidVerifications,
            'verifiedNidVerifications' => $this->verifiedNidVerifications
        ])->layout('layouts.base');
    }
}
