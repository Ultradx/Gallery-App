import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
    // server: {
    //     host: "0.0.0.0", // allow access from outside localhost
    //     hmr: {
    //         host: "unadjustable-vernon-pseudoaffectionate.ngrok-free.dev", // your Ngrok subdomain
    //         protocol: "wss",           // secure websocket for hot reload
    //     },
    // },
});
