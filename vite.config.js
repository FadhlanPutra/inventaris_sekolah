import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
// import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/tour.js',
                'resources/js/darkMode.js',
                'resources/css/filament/dashboard/themes/pesat.css',
            ],
            refresh: true,
        }),
        // tailwindcss(),
    ],
    server: {
        cors: true,
    },
});