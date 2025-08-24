<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;

/**
 * Trait ini mengandalkan properti static $cacheClearUrls di model yang memakainya.
 *
 * @property static array $cacheClearUrls
 */


trait ClearsResponseCache
{
    // protected static array $cacheClearUrls = []; // default kosong biar nggak error
    public static function bootClearsResponseCache()
    {
        // static::saved(fn() => ResponseCache::clear());
        // static::deleted(fn() => ResponseCache::clear());

        // static::created(fn() => ResponseCache::clear());
        // static::updated(fn() => ResponseCache::clear());
        // static::deleted(fn() => ResponseCache::clear());

        static::saved(function ($model) {
            if ($model->wasChanged()) {
                $model->clearCacheForUrls();
            }
        });

        static::deleted(function ($model) {
            $model->clearCacheForUrls();
        });
    }

    protected function clearCacheForUrls()
    {
        if (!property_exists($this, 'cacheClearUrls')) {
            return;
        }

        // Kalau $cacheClearUrls nya merah itu emang intelephense nya. biarin aja gak bakal ngebug (katanya). tapi kalau ngebug ya benerin 😁
        // "Akan muncul garis merah di $cacheClearUrls karena sifat trait yang memanggil properti dari luar (model)." ~AI
        foreach (static::$cacheClearUrls as $url) {
            ResponseCache::forget($url);
        }
    }
}
