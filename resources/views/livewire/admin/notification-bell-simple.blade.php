{{-- Simple notification bell template --}}
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 rounded-full">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
        @endif
    </button>
    
    <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50">
        <div class="p-4 border-b">
            <h3 class="font-semibold">Notifikasi</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications ?? [] as $notification)
                <div class="p-4 border-b hover:bg-gray-50">
                    <div class="text-sm font-medium">{{ $notification['data']['title'] ?? 'Notifikasi' }}</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $notification['data']['body'] ?? 'Tidak ada deskripsi' }}</div>
                    <div class="text-xs text-gray-500 mt-2">{{ $notification['created_at_human'] ?? 'Baru saja' }}</div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <p>Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>