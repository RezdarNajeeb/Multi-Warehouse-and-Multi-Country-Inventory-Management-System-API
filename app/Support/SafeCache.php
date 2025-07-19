<?php

namespace App\Support;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;

class SafeCache
{
    public static function remember(string $key, int $ttl, Closure $callback, array $tags = ['products'])
    {
        try {
            $store = self::getStorePlace();

            $cache = Cache::store($store);
            if ($store !== 'array') {
                $cache = $cache->tags($tags);
            }

            return $cache->remember($key, $ttl, $callback);
        } catch (Exception $e) {
            logger()->warning($e->getMessage());
            logger()->warning("Cache unavailable, serving fresh data. Key: {$key}");
            return $callback();
        }
    }

    public static function flushTag(string $tag): void
    {
        try {
            $store = self::getStorePlace();

            if ($store !== 'array') {
                Cache::store($store)->tags([$tag])->flush();
            }
        } catch (Exception $e) {
            logger()->warning($e->getMessage());
            logger()->warning("Could not flush cache tag: {$tag}");
        }
    }

    private static function getStorePlace(): string
    {
        return app()->environment('testing') ? 'array' : 'redis';
    }
}
