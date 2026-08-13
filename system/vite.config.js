import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'node:fs';
import path from 'node:path';

function collectModuleAssetInputs() {
    const modulesRoot = path.resolve('app/Modules');

    if (!fs.existsSync(modulesRoot)) {
        return [];
    }

    return fs.readdirSync(modulesRoot, { withFileTypes: true })
        .filter((entry) => entry.isDirectory())
        .flatMap((entry) => {
            const assetsDir = path.join(modulesRoot, entry.name, 'Resources', 'assets');

            if (!fs.existsSync(assetsDir)) {
                return [];
            }

            return walkFiles(assetsDir)
                .filter((file) => file.endsWith('.js') || file.endsWith('.css'))
                .map((file) => path.relative(process.cwd(), file).replaceAll('\\', '/'));
        });
}

function walkFiles(directory) {
    return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const fullPath = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            return walkFiles(fullPath);
        }

        return [fullPath];
    });
}

const moduleAssetInputs = collectModuleAssetInputs();

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/components/frontend-menu-builder.js',
                ...moduleAssetInputs,
            ],
            // App runs root-based: the web root is one level above /system,
            // and compiled assets are served from /assets/build.
            publicDirectory: '..',
            buildDirectory: 'assets/build',
            hotFile: '../assets/build/hot',
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // The build dir (assets/build) lives outside the Vite root, so Vite won't
        // clear it by default. Opt in so each build removes stale hashed assets
        // from previous builds instead of letting them pile up.
        emptyOutDir: true,
        chunkSizeWarningLimit: 600,
        rollupOptions: {
            output: {
                manualChunks: {
                    apexcharts: ['apexcharts'],
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
