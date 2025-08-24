<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>401 Unauthorized | {{ config('app.name') }}</title>

    {{-- Preload fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    {{-- Styles & Scripts dari Vite --}}
    @vite(['resources/css/app.css', 'resources/js/darkMode.js'])
</head>
<body class="h-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-950 font-sans antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-32 w-96 h-96 rounded-full bg-yellow-200/10 dark:bg-yellow-500/5 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-yellow-300/10 dark:bg-yellow-400/5 blur-3xl"></div>
    </div>

    <main class="relative min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-2xl mx-auto">
            <!-- Error Card -->
            <div class="bg-white dark:bg-zinc-800 backdrop-blur-xl rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <!-- Header Section -->
                <div class="relative px-8 py-12 text-center">
                    <!-- Error Icon -->
                    <div class="mx-auto mb-6 w-24 h-24 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <!-- Error Code -->
                    <h1 class="mb-4 text-8xl lg:text-9xl font-black tracking-tighter bg-gradient-to-r from-yellow-500 to-yellow-600 bg-clip-text text-transparent">
                        401
                    </h1>

                    <!-- Error Title -->
                    <h2 class="mb-6 text-3xl md:text-4xl font-bold text-zinc-900 dark:text-white">
                        Akses Tidak Diizinkan
                    </h2>

                    <!-- Error Description -->
                    <p class="mb-8 text-lg text-zinc-600 dark:text-zinc-300 leading-relaxed max-w-md mx-auto">
                        Anda tidak memiliki izin untuk mengakses halaman ini. Silakan masuk terlebih dahulu atau kembali ke halaman utama.
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a 
                            href="{{ route('login') }}" 
                            class="group inline-flex items-center justify-center px-8 py-4 text-white font-semibold rounded-2xl bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-yellow-300/50 dark:focus:ring-yellow-500/50"
                        >
                            <svg class="w-5 h-5 mr-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Masuk
                        </a>
                        
                        <a 
                            href="{{ url('/') }}" 
                            class="group inline-flex items-center justify-center px-8 py-4 text-zinc-700 dark:text-zinc-200 font-semibold rounded-2xl bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl border border-zinc-200 dark:border-zinc-600 focus:outline-none focus:ring-4 focus:ring-zinc-300/50 dark:focus:ring-zinc-500/50"
                        >
                            <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Halaman Utama
                        </a>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="px-8 py-6 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-600">
                    <div class="flex items-center justify-center text-sm text-zinc-500 dark:text-zinc-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Butuh bantuan? Hubungi administrator sistem
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Dark mode toggle (optional) --}}
    <button 
        type="button"
        id="theme-toggle"
        class="fixed top-4 right-4 p-3 rounded-2xl bg-white dark:bg-zinc-800 backdrop-blur-xl border border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all duration-200 shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-zinc-300/50 dark:focus:ring-zinc-500/50"
        aria-label="Toggle dark mode"
    >
        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
    </button>
</body>
</html>