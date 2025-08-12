<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
  <title>500 Internal Server Error | {{ config('app.name') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/darkMode.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300">
  <section class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md mx-auto text-center space-y-6">
      <h1 class="text-7xl lg:text-9xl font-extrabold text-red-600 dark:text-red-500">500</h1>
      <h2 class="text-2xl md:text-3xl font-bold">Internal Server Error</h2>
      <p class="text-lg text-gray-600 dark:text-gray-400 font-light">
        Terjadi kesalahan pada server. Tim kami sedang memperbaikinya.
      </p>
      <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
        <button onclick="location.reload()"
          class="px-6 py-3 bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 rounded-lg text-white font-medium transition hover:cursor-pointer">
          Muat Ulang
        </button>
        <button onclick="window.location.href='mailto:qqwuguh@gmail.com?subject=Error%20500&body=Website%20{{ urlencode(url()->full()) }}%20mengalami%20error'"
          class="px-6 py-3 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-400 dark:focus:ring-gray-600 rounded-lg text-gray-800 dark:text-gray-100 font-medium transition hover:cursor-pointer">
          Hubungi Admin
        </button>
      </div>
      <p class="mt-8 text-sm text-gray-500 dark:text-gray-500">
        Silakan coba lagi beberapa saat nanti.
      </p>
    </div>
  </section>
</body>
</html>