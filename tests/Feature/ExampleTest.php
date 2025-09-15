<?php

test('returns a successful response', function () {
    $response = $this->get('/dashboard/login');

    $response->assertStatus(200);
});