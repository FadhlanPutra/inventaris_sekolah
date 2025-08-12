<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

  <title>404 Not Found | {{ config('app.name') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/darkMode.js'])
</head>
<body id="themed" class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-300">
  <section class="flex items-center justify-center min-h-screen px-4">
    <div class="text-center space-y-6 max-w-md mx-auto">
      <h1 class="text-9xl font-extrabold text-yellow-600 dark:text-yellow-500">404</h1>
      <h2 class="text-3xl font-bold">Halaman Tidak Ditemukan</h2>
      <p class="text-lg text-gray-500 dark:text-gray-400">
        Halaman yang kamu cari tidak ditemukan. Periksa kembali alamat URL atau kembali ke halaman sebelumnya.
      </p>
      <button
        onclick="history.back()"
        class="mt-6 inline-flex items-center justify-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 dark:focus:ring-yellow-800 rounded-lg text-white font-medium transition hover:cursor-pointer">
        Kembali
      </button>
      <div class="mt-8 text-gray-400 dark:text-gray-500 text-sm">
        Atau <a href="{{ url('/') }}" class="text-yellow-600 dark:text-yellow-500 hover:underline">kembali ke beranda</a>.
      </div>
    </div>
  </section>
</body>
</html>
