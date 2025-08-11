<?php

namespace App\CacheProfiles;

use Spatie\ResponseCache\CacheProfiles\CacheProfile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentCacheProfile implements CacheProfile
{
    public function enabled(Request $request): bool
    {
        return config('responsecache.enabled');
    }

    public function shouldCacheRequest(Request $request): bool
    {
        return $request->isMethod('GET')
            && !$request->ajax()
            && !$request->is('filament/*');
    }

    public function shouldCacheResponse(Response $response): bool
    {
        return $response->isSuccessful()
            && str_starts_with($response->headers->get('Content-Type', ''), 'text/');
    }

    public function cacheRequestUntil(Request $request): \DateTime
    {
        return now()->addSeconds(config('responsecache.cache_lifetime_in_seconds', 0));
    }

    public function useCacheNameSuffix(Request $request): string
    {
        // Sisipkan versi aplikasi sebagai suffix cache key
        return config('app.version') .
            ($request->user() ? '-user:' . (string)$request->user()->getKey() : '');
    }
}
