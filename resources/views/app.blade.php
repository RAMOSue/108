<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Tribal System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased bg-gradient-to-r from-blue-100 to-green-100 min-h-screen">
    @inertia
</body>
<script type="module">
    import {
        Ziggy
    } from '@ziggy-js/ziggy';
    import {
        ZiggyVue
    } from 'ziggy-vue';
    import {
        createApp
    } from 'vue';
    import App from './App.vue';

    const app = createApp(App);
    app.use(ZiggyVue, Ziggy);
    app.mount('#app');
</script>

</html>