<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;

trait ClearsResponseCache
{
    public static function bootClearsResponseCache()
    {
        // static::saved(fn() => ResponseCache::clear());
        // static::deleted(fn() => ResponseCache::clear());

        static::created(fn() => ResponseCache::clear());
        static::updated(fn() => ResponseCache::clear());
        static::deleted(fn() => ResponseCache::clear());
    }
}
