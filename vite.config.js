import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        outDir: 'public',
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'resources/js/app.ts'),
                ssr: path.resolve(__dirname, 'resources/js/ssr.ts'),
            },
        },
    },
    resolve: {
        alias: {
            '@axios': path.resolve(__dirname, 'resources/js/lib/axios'),
        },
    },
});
