<?php

namespace App\Http\Livewire\ServiceProvider;

use Livewire\Component;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class DashboardComponent extends Component
{
    public $newPendingRequests;
    public $pendingRequests;
    public $acceptedRequests;
    public $completedRequests;

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $serviceProviderId = Auth::id();

        // Fetch new pending requests first
        $this->newPendingRequests = ServiceRequest::where('service_provider_id', $serviceProviderId)
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->pendingRequests = ServiceRequest::where('service_provider_id', $serviceProviderId)
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->with(['customer', 'service'])
            ->get();

        $this->acceptedRequests = ServiceRequest::where('service_provider_id', $serviceProviderId)
            ->where('status', ServiceRequest::STATUS_ACCEPTED)
            ->with(['customer', 'service'])
            ->get();

        $this->completedRequests = ServiceRequest::where('service_provider_id', $serviceProviderId)
            ->where('status', ServiceRequest::STATUS_COMPLETED)
            ->with(['customer', 'service'])
            ->get();
    }

    public function acceptRequest($requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);
        $serviceRequest->status = ServiceRequest::STATUS_ACCEPTED;
        $serviceRequest->save();

        // Refresh the requests
        $this->loadRequests();

        session()->flash('success', 'Service request accepted successfully!');
    }

    public function rejectRequest($requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);
        $serviceRequest->status = ServiceRequest::STATUS_REJECTED;
        $serviceRequest->save();

        // Refresh the requests
        $this->loadRequests();

        session()->flash('error', 'Service request rejected.');
    }

    public function render()
    {
        return view('livewire.service-provider.dashboard-component', [
            'newPendingRequests' => $this->newPendingRequests,
            'pendingRequests' => $this->pendingRequests,
            'acceptedRequests' => $this->acceptedRequests,
            'completedRequests' => $this->completedRequests
        ])->layout('layouts.base');
    }
}
