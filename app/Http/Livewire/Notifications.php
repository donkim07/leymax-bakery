<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Carbon;

class Notifications extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

    protected $listeners = ['echo:private-App.Models.User.{userId},Notification' => 'handleNotification'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();
        $this->notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'time' => Carbon::parse($notification->created_at)->diffForHumans(),
                    'action_url' => $this->getNotificationUrl($notification),
                    'read' => !is_null($notification->read_at),
                ];
            })
            ->toArray();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function handleNotification($notification)
    {
        $this->loadNotifications();
        $this->emit('notificationReceived');
    }

    public function markAsRead($notificationId)
    {
        auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        $this->loadNotifications();
    }

    protected function getNotificationTitle($notification)
    {
        return match ($notification->type) {
            'App\Notifications\StockAlert' => 'Low Stock Alert',
            'App\Notifications\OrderReceived' => 'New Order Received',
            'App\Notifications\PaymentReceived' => 'Payment Received',
            'App\Notifications\CourseEnrollment' => 'New Course Enrollment',
            default => class_basename($notification->type),
        };
    }

    protected function getNotificationMessage($notification)
    {
        return $notification->data['message'] ?? '';
    }

    protected function getNotificationUrl($notification)
    {
        return $notification->data['action_url'] ?? '#';
    }

    public function render()
    {
        return view('livewire.notifications');
    }
} 