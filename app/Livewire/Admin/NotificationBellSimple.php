<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBellSimple extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showDropdown = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = [];
        $this->unreadCount = 0;
        
        if (Auth::check()) {
            $user = Auth::user();
            
            // Simple direct query tanpa cache
            $this->notifications = $user->notifications()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'data' => $notification->data ?? [],
                        'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
                        'created_at_human' => $notification->created_at->diffForHumans(),
                    ];
                })
                ->toArray();

            $this->unreadCount = $user->unreadNotifications()->count();
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        if (Auth::check() && $notificationId) {
            Auth::user()->notifications()
                ->where('id', $notificationId)
                ->update(['read_at' => now()]);
            
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications()
                ->update(['read_at' => now()]);
            
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('livewire.admin.notification-bell-simple');
    }
}