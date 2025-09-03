<?php

use Illuminate\Support\Facades\URL;

/**
 * Cek apakah sedang jalan di local dev (misalnya localhost:8000).
 * Kalau iya, pakai asset biasa (http).
 * Kalau bukan, pakai secure_asset (https).
 */
if (!function_exists('is_local_dev')) {
    function is_local_dev(): bool
    {
        $localHosts = ['127.0.0.1', 'localhost'];
        $localPorts = [8000, 5173]; // tambahin kalau ada port lain (misalnya vite)

        return in_array(request()->getHost(), $localHosts)
            && in_array(request()->getPort(), $localPorts);
    }
}

if (!function_exists('app_asset')) {
    /**
     * Generate the correct asset URL.
     * Kalau bukan local dev → secure_asset.
     */
    function app_asset(string $path): string
    {
        return is_local_dev()
            ? asset($path)
            : secure_asset($path);
    }
}

if (!function_exists('app_url')) {
    /**
     * Generate URL sesuai environment/host.
     */
    function app_url(string $path = ''): string
    {
        return is_local_dev()
            ? URL::to($path)
            : URL::secure($path);
    }
}
