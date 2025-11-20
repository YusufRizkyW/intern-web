{{-- filepath: resources/views/livewire/admin/notification-bell.blade.php --}}
<div>
    {{-- Main notification bell component --}}
    <div class="relative" x-data="{ open: false }" x-init="$watch('open', value => console.log('Dropdown open:', value))">
        {{-- Bell Icon --}}
        <button 
            @click="open = !open"
            class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-full"
            aria-label="Notifications"
        >
            {{-- Bell SVG --}}
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>

            {{-- Badge --}}
            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem] h-5">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </button>

        {{-- Dropdown dengan animasi yang diperbaiki --}}
        <div 
            x-show="open"
            x-cloak
            @click.away="open = false"
            @keydown.escape.window="open = false"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 origin-top-right"
        >
            {{-- Header --}}
            <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                        Notifikasi
                    </h3>
                    @if($unreadCount > 0)
                        <button 
                            wire:click="markAllAsRead"
                            @click="$wire.markAllAsRead()"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition-colors duration-200"
                        >
                            Tandai Semua Dibaca
                        </button>
                    @endif
                </div>
                @if($unreadCount > 0)
                    <div class="mt-2 text-xs text-gray-500">
                        {{ $unreadCount }} notifikasi belum dibaca
                    </div>
                @endif
            </div>

            {{-- Notification List --}}
            <div class="max-h-96 overflow-y-auto notification-list">
                @forelse($notifications as $notification)
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 {{ $notification['read_at'] ? 'opacity-75' : 'bg-blue-50/30' }}">
                        <div class="flex items-start gap-3">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 mt-1">
                                @if(str_contains($notification['type'], 'NewPendaftaran'))
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center ring-2 ring-green-200">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center ring-2 ring-blue-200">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-800 {{ !$notification['read_at'] ? 'font-semibold' : '' }}">
                                    {{ $notification['data']['title'] ?? 'Notifikasi' }}
                                </div>
                                <div class="text-xs text-gray-600 mt-1 notification-text-clamp">
                                    {{ $notification['data']['body'] ?? 'Tidak ada deskripsi' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $notification['created_at']->diffForHumans() }}
                                </div>
                            </div>

                            {{-- Mark as read button --}}
                            @if(!$notification['read_at'])
                                <button 
                                    wire:click="markAsRead('{{ $notification['id'] }}')"
                                    @click="$wire.markAsRead('{{ $notification['id'] }}')"
                                    class="flex-shrink-0 text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors duration-200"
                                    title="Tandai dibaca"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            @else
                                <div class="flex-shrink-0 text-green-600 p-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-sm font-medium">Tidak ada notifikasi</p>
                        <p class="text-xs text-gray-400 mt-1">Semua notifikasi akan muncul di sini</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer --}}
            @if(count($notifications) > 0)
                <div class="p-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <a href="{{ route('filament.admin.pages.dashboard') }}" 
                       @click="open = false"
                       class="block text-center text-xs text-blue-600 hover:text-blue-800 font-medium py-2 px-4 rounded hover:bg-blue-50 transition-colors duration-200">
                        Lihat Semua Pendaftar
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Inline styles untuk komponen --}}
    <style>
        [x-cloak] { 
            display: none !important; 
        }
        
        .notification-text-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Smooth scrollbar untuk dropdown */
        .notification-list::-webkit-scrollbar {
            width: 4px;
        }
        
        .notification-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .notification-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        .notification-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    {{-- JavaScript untuk real-time updates --}}
    <script>
        document.addEventListener('alpine:init', () => {
            // Refresh notifications every 30 seconds dengan debounce
            let refreshInterval;
            
            const refreshNotifications = () => {
                if (typeof Livewire !== 'undefined' && @this) {
                    @this.call('loadNotifications');
                }
            };
            
            // Set interval
            refreshInterval = setInterval(refreshNotifications, 30000);
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            });
            
            // Debug log
            console.log('Notification Bell initialized');
        });
    </script>
</div>
