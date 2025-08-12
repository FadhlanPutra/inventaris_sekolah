<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>502 Bad Gateway | {{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/darkMode.js'])

</head>
<body id="themed" class="w-full">
    <section class="bg-white dark:bg-gray-900 h-screen flex items-center">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="mx-auto max-w-screen-sm text-center">
                <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl text-red-600 dark:text-red-500">502</h1>
                <p class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white">Bad Gateway</p>
                <p class="mb-4 text-lg font-light text-gray-500 dark:text-gray-400">
                    Server kami sedang mengalami gangguan gateway. Silakan coba lagi nanti.
                </p>
            </div>   
        </div>
    </section>
</body>
</html>
