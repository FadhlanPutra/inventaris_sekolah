<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>500 Internal Server Error | {{ config('app.name') }}</title>

    {{-- Preload fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    {{-- Styles & Scripts dari Vite --}}
    @vite(['resources/css/app.css', 'resources/js/darkMode.js'])

    <style>
        .dark { color-scheme: dark; }
        .backdrop-blur-xl { backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); }
        .bg-clip-text { -webkit-background-clip: text; background-clip: text; }
    </style>
</head>
<body class="h-full min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-950 font-sans antialiased">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-32 w-96 h-96 rounded-full bg-red-200/10 dark:bg-red-500/5 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-red-300/10 dark:bg-red-400/5 blur-3xl"></div>
    </div>

    <main class="relative min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-2xl mx-auto">
            <div class="bg-white dark:bg-zinc-800 backdrop-blur-xl rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="relative px-8 py-12 text-center">
                    <!-- Error Icon -->
                    <div class="mx-auto mb-6 w-24 h-24 rounded-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="12" y1="17" x2="12.01" y2="17" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <!-- Error Code -->
                    <h1 class="mb-4 text-8xl lg:text-9xl font-black tracking-tighter bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">
                        500
                    </h1>

                    <!-- Error Title -->
                    <h2 class="mb-6 text-3xl md:text-4xl font-bold text-zinc-900 dark:text-white">
                        Internal Server Error
                    </h2>

                    <!-- Error Description -->
                    <p class="mb-8 text-lg text-zinc-600 dark:text-zinc-300 leading-relaxed max-w-md mx-auto">
                        An error occurred on our server.
                            Our team is working to fix it.
                            Please try again later.
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a
                            href="{{ url('/') }}"
                            class="group inline-flex items-center justify-center px-8 py-4 text-white font-semibold rounded-2xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-red-300/50 dark:focus:ring-red-500/50"
                        >
                            <svg class="w-5 h-5 mr-2 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                           Main page
                        </a>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="px-8 py-6 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-600">
                    <div class="flex items-center justify-center text-sm text-zinc-500 dark:text-zinc-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        The server is experiencing issues, please try again later.
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Dark mode toggle --}}
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
