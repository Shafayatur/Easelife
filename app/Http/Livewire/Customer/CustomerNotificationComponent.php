<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerNotificationComponent extends Component
{
    public $notifications;
    public $unreadCount;
    public $selectedNotification = null;

    protected $listeners = [
        'refreshNotifications' => 'loadNotifications'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            $this->notifications = collect();
            $this->unreadCount = 0;
            return;
        }

        // Fetch booking-related notifications for the current customer
        $this->notifications = Notification::where('user_id', Auth::id())
            ->where('type', 'like', 'booking_%')
            ->orderBy('created_at', 'desc')
            ->get();

        // Count unread notifications
        $this->unreadCount = $this->notifications
            ->where('status', Notification::STATUS_UNREAD)
            ->count();

        // Log notification details for debugging
        Log::info('Customer Notifications Loaded', [
            'user_id' => Auth::id(),
            'total_notifications' => $this->notifications->count(),
            'unread_count' => $this->unreadCount
        ]);
    }

    public function markAsRead($notificationId)
    {
        try {
            $notification = Notification::findOrFail($notificationId);
            
            // Ensure the notification belongs to the current user
            if ($notification->user_id !== Auth::id()) {
                Log::warning('Unauthorized notification access attempt', [
                    'user_id' => Auth::id(),
                    'notification_id' => $notificationId
                ]);
                return;
            }

            // Mark as read
            $notification->update(['status' => Notification::STATUS_READ]);

            // Refresh notifications
            $this->loadNotifications();

            Log::info('Notification marked as read', [
                'notification_id' => $notificationId,
                'user_id' => Auth::id()
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read', [
                'error' => $e->getMessage(),
                'notification_id' => $notificationId
            ]);
        }
    }

    public function viewNotificationDetails($notificationId)
    {
        try {
            $this->selectedNotification = Notification::with('relatedModel')
                ->findOrFail($notificationId);

            // Ensure the notification belongs to the current user
            if ($this->selectedNotification->user_id !== Auth::id()) {
                $this->selectedNotification = null;
                return;
            }

            // If not already read, mark as read
            if ($this->selectedNotification->status === Notification::STATUS_UNREAD) {
                $this->markAsRead($notificationId);
            }
        } catch (\Exception $e) {
            Log::error('Error viewing notification details', [
                'error' => $e->getMessage(),
                'notification_id' => $notificationId
            ]);
            $this->selectedNotification = null;
        }
    }

    public function render()
    {
        return view('livewire.customer.customer-notification-component');
    }
}
