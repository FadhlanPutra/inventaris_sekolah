<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::get('/dashboard', function () {
//     return redirect()->route('filament.dashboard.pages.dashboard');
// })->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

// Route::get('/400', function () {
//     return abort(400);
// });
// Route::get('/401', function () {
//     return abort(401);
// });
// Route::get('/403', function () {
//     return abort(403);
// });
// Route::get('/404', function () {
//     return abort(404);
// });
// Route::get('/419', function () {
//     return abort(419);
// });
// Route::get('/500', function () {
//     return abort(500);
// });
// Route::get('/503', function () {
//     return abort(503);
// });

require __DIR__.'/auth.php';
