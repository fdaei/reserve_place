import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.less',
                'resources/css/template/admin-app.less',
                'resources/css/user/add-residence.less',
                'resources/css/user/dashboard.less',
                'resources/css/user/detail.less',
                'resources/css/user/index.less',
                'resources/css/user/login.less',
                'resources/css/user/profile.less',
                'resources/css/user/tickets.less',
            ],
            refresh: true,
        }),
    ],
});
