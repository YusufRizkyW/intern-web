<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class CacheOptimizationService
{
    /**
     * Clear notification cache for specific user
     */
    public static function clearUserNotificationCache(int $userId): void
    {
        Cache::forget('user_notifications_' . $userId);
        Cache::forget('user_unread_count_' . $userId);
    }

    /**
     * Clear notification cache for all admin users
     */
    public static function clearAllAdminNotificationCache(): void
    {
        $adminUsers = User::where('role', 'admin')->pluck('id');
        
        foreach ($adminUsers as $adminId) {
            self::clearUserNotificationCache($adminId);
        }
    }

    /**
     * Clear notification cache with debouncing
     * Prevents multiple cache clears in short time
     */
    public static function clearAdminNotificationCacheDebounced(): void
    {
        $lockKey = 'cache_clear_admin_notifications';
        $lockTimeout = 10; // seconds
        
        // Only clear if not already cleared recently
        if (!Cache::has($lockKey)) {
            Cache::put($lockKey, true, $lockTimeout);
            self::clearAllAdminNotificationCache();
        }
    }

    /**
     * Get cached notifications with smart refresh
     */
    public static function getCachedNotifications(int $userId, int $cacheTime = 30): array
    {
        $cacheKey = 'user_notifications_' . $userId;
        
        return Cache::remember($cacheKey, $cacheTime, function () use ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return [];
            }

            return $user->notifications()
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
        });
    }

    /**
     * Get cached unread count with smart refresh
     */
    public static function getCachedUnreadCount(int $userId, int $cacheTime = 30): int
    {
        $cacheKey = 'user_unread_count_' . $userId;
        
        return Cache::remember($cacheKey, $cacheTime, function () use ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return 0;
            }

            return $user->unreadNotifications()->count();
        });
    }
}