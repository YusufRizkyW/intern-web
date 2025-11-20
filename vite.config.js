import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pendaftaran.js',
                'resources/js/dashboard.js',
                'resources/css/filament/admin/theme.css', // ✅ Custom Filament theme
            ],
            refresh: [
                ...refreshPaths,
                'app/Filament/**',
                'app/Livewire/**',
            ],
        }),
    ],
    // ✅ PERFORMANCE OPTIMIZATIONS
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                }
            }
        },
        chunkSizeWarningLimit: 1000,
        sourcemap: false, // Disable sourcemap di production
    },
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: false, // Disable polling untuk better performance
        }
    },
    optimizeDeps: {
        include: ['alpinejs', '@alpinejs/focus', '@alpinejs/collapse']
    }
});
