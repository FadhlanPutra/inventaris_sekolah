<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;

/**
 * Trait ini menghapus cache untuk URL tertentu setiap kali model berubah.
 *
 * Properti statis $cacheClearUrls harus didefinisikan di model yang menggunakan trait ini:
 *
 * protected static array $cacheClearUrls = [
 *     '/homepage',
 *     '/products',
 * ];
 */
trait ClearsResponseCache
{
    /**
     * Boot trait untuk mengikat event Eloquent.
     */
    public static function bootClearsResponseCache(): void
    {
        // Clear cache saat model disimpan (create/update)
        static::saved(function ($model) {
            $model->clearCacheForUrls();
        });

        // Clear cache saat model dihapus
        static::deleted(function ($model) {
            $model->clearCacheForUrls();
        });
    }

    /**
     * Hapus cache untuk URL yang ditentukan di $cacheClearUrls
     */
    protected function clearCacheForUrls(): void
    {
        if (!isset(static::$cacheClearUrls) || !is_array(static::$cacheClearUrls)) {
            // Tidak ada URL yang didefinisikan, langsung return
            return;
        }

        foreach (static::$cacheClearUrls as $url) {
            if (!empty($url) && is_string($url)) {
                ResponseCache::forget($url);
            }
        }
    }
}
