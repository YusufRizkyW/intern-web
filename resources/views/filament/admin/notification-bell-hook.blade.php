{{-- filepath: resources/views/filament/admin/notification-bell-hook.blade.php --}}

{{-- Opsi 1: Jika component di app/Http/Livewire --}}
{{-- <livewire:notification-bell /> --}}

{{-- Opsi 2: Jika component di app/Livewire/Admin --}}
<livewire:admin.notification-bell />

{{-- Opsi 3: Langsung include view tanpa Livewire --}}
{{-- @include('admin.notifications.bell') --}}
