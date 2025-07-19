<?php

namespace App\Support;

namespace App\Support;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;

class SafeCache
{
    public static function remember(string $key, int $ttl, Closure $callback)
    {
        try {
            return Cache::store('redis')->remember($key, $ttl, $callback);
        } catch (Exception $e) {
            logger()->warning($e->getMessage());
            logger()->warning("Redis unavailable, serving fresh data. Key: {$key}");
            return $callback();
        }
    }

    public static function flushTag(string $tag): void
    {
        try {
            Cache::store('redis')->tags([$tag])->flush();
        } catch (Exception $e) {
            logger()->warning($e->getMessage());
            logger()->warning("Redis unavailable — could not flush cache tag: {$tag}");
        }
    }
}

