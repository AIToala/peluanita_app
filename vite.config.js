import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'node:path';
import { defineConfig } from 'vite';

function isExternal(id) {
    return id.startsWith('~') && !path.isAbsolute(id);
}

export default defineConfig({
    base: '/',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
            buildDirectory: 'dist',
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
    resolve: {
        alias: {
            '@axios': path.resolve(__dirname, 'resources/js/lib/axios'),
        },
    },
    build: {
        outDir: 'public/dist',
        sourcemap: true,
        emptyOutDir: false,
        rollupOptions: {
            external: isExternal,
            output: {
                assetFileNames: 'assets/[name][extname]',
                manualChunks(id) {
                    if (isExternal(id)) {
                        return 'vendor';
                    }
                },
            },
        },
    },
    server: {
        https: true,
    },
});
