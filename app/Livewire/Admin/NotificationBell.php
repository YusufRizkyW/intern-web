<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
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
        // Inisialisasi default values
        $this->notifications = [];
        $this->unreadCount = 0;
        
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        
        try {
            // Coba gunakan cache service terlebih dahulu
            $this->notifications = \App\Services\CacheOptimizationService::getCachedNotifications($user->id, 30);
            $this->unreadCount = \App\Services\CacheOptimizationService::getCachedUnreadCount($user->id, 30);
            
        } catch (\Exception $e) {
            // Fallback ke direct query jika cache service error
            try {
                $this->notifications = $user->notifications()
                    ->take(10)
                    ->get()
                    ->map(function ($notification) {
                        $createdAt = $notification->created_at;
                        return [
                            'id' => $notification->id,
                            'type' => $notification->type,
                            'data' => $notification->data ?? [],
                            'read_at' => $notification->read_at ? $notification->read_at->toISOString() : null,
                            'created_at' => $createdAt->toISOString(),
                            'created_at_human' => $createdAt->diffForHumans(),
                        ];
                    })
                    ->toArray();

                $this->unreadCount = $user->unreadNotifications()->count();
                
            } catch (\Exception $fallbackError) {
                // Ultimate fallback - log error dan return empty
                \Illuminate\Support\Facades\Log::error('NotificationBell complete failure: ' . $fallbackError->getMessage());
                $this->notifications = [];
                $this->unreadCount = 0;
            }
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->notifications()
                ->where('id', $notificationId)
                ->update(['read_at' => now()]);
            
            // Clear cache untuk memastikan data fresh
            $this->clearNotificationCache($user->id);
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->unreadNotifications()
                ->update(['read_at' => now()]);
            
            // Clear cache untuk memastikan data fresh
            $this->clearNotificationCache($user->id);
            $this->loadNotifications();
        }
    }

    /**
     * Clear notification cache menggunakan service
     */
    private function clearNotificationCache($userId)
    {
        \App\Services\CacheOptimizationService::clearUserNotificationCache($userId);
    }

    public function render()
    {
        return view('livewire.admin.notification-bell');
    }
}
