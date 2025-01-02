<?php

namespace App\Http\Livewire\ServiceProvider;

use Livewire\Component;
use App\Models\Notification;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class NotificationComponent extends Component
{
    public $notifications;
    public $unreadCount;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->unreadCount = $this->notifications->where('status', Notification::STATUS_UNREAD)->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->update(['status' => Notification::STATUS_READ]);
        $this->loadNotifications();
    }

    public function viewServiceRequest($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        
        // Mark as read
        $notification->update(['status' => Notification::STATUS_READ]);

        // Redirect to service request details page
        return redirect()->route('service_provider.service_request.details', [
            'serviceRequestId' => $notification->related_model_id
        ]);
    }

    public function acceptServiceRequest($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        
        if ($notification->related_model_type === ServiceRequest::class) {
            $serviceRequest = ServiceRequest::findOrFail($notification->related_model_id);
            
            // Update service request status
            $serviceRequest->update([
                'status' => ServiceRequest::STATUS_ACCEPTED
            ]);
            
            // Create a new notification for the customer
            Notification::create([
                'user_id' => $serviceRequest->customer_id,
                'type' => Notification::TYPE_SERVICE_REQUEST_ACCEPTED,
                'message' => "Your service request for {$serviceRequest->service->name} has been accepted.",
                'related_model_type' => ServiceRequest::class,
                'related_model_id' => $serviceRequest->id,
                'status' => Notification::STATUS_UNREAD
            ]);
            
            // Mark current notification as read
            $notification->update(['status' => Notification::STATUS_READ]);
            
            session()->flash('message', 'Service request accepted successfully.');
        }
    }

    public function rejectServiceRequest($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        
        if ($notification->related_model_type === ServiceRequest::class) {
            $serviceRequest = ServiceRequest::findOrFail($notification->related_model_id);
            
            // Update service request status
            $serviceRequest->update([
                'status' => ServiceRequest::STATUS_REJECTED
            ]);
            
            // Create a new notification for the customer
            Notification::create([
                'user_id' => $serviceRequest->customer_id,
                'type' => Notification::TYPE_SERVICE_REQUEST_REJECTED,
                'message' => "Your service request for {$serviceRequest->service->name} has been rejected.",
                'related_model_type' => ServiceRequest::class,
                'related_model_id' => $serviceRequest->id,
                'status' => Notification::STATUS_UNREAD
            ]);
            
            // Mark current notification as read
            $notification->update(['status' => Notification::STATUS_READ]);
            
            session()->flash('message', 'Service request rejected successfully.');
        }
    }

    public function render()
    {
        return view('livewire.service-provider.notification-component', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount
        ])->layout('layouts.base');
    }
}
