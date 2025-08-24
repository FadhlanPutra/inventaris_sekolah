<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/dashboard/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);

    $this->get('/dashboard')->assertStatus(200);
});