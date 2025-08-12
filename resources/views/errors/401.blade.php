<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>401 Unauthorized | {{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/darkMode.js'])
</head>
<body id="themed" class="w-full">
    <section class="bg-white dark:bg-gray-900 h-screen flex items-center">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="mx-auto max-w-screen-sm text-center">
                <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl text-yellow-600 dark:text-yellow-500">401</h1>
                <p class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white">Unauthorized</p>
                <p class="mb-4 text-lg font-light text-gray-500 dark:text-gray-400">
                    Kamu tidak memiliki izin untuk mengakses halaman ini. Silakan login terlebih dahulu atau kembali ke halaman utama.
                </p>
                <a 
                    href="{{ route('login') }}" 
                    class="inline-flex text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-yellow-800 my-4"
                >
                    Login
                </a>
                <a 
                    href="{{ url('/') }}" 
                    class="ml-2 inline-flex text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-gray-700 my-4"
                >
                    Halaman Utama
                </a>
            </div>   
        </div>
    </section>
</body>
</html>
