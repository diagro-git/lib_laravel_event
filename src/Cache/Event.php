<?php
namespace Diagro\Events\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Class for caching the events.
 */
class Event
{


    public static function getCacheKey($user_id): string
    {
        return 'events.' . $user_id;
    }


    public static function putInCache($event)
    {
        $prefix = config('cache.prefix');
        config()->set('cache.prefix', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache');
        $cacheKey = self::getCacheKey($event->user_id);
        $value = [$event];

        //merge with existence events in the cache
        if(Cache::has($cacheKey)) {
            $cachedValue = Cache::get($cacheKey);
            if(is_array($cachedValue)) {
                $value = array_merge($cachedValue, $value);
            }
        }

        Cache::put($cacheKey, $value);
        config()->set('cache.prefix', $prefix);
    }


    public static function getCachedEvents($user_id): array
    {
        $prefix = config('cache.prefix');
        config()->set('cache.prefix', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache');
        $events = [];
        $cacheKey = self::getCacheKey($user_id);

        if(Cache::has($cacheKey)) {
            $cacheValue = Cache::pull($cacheKey);
            if(is_array($cacheValue)) {
                $events = $cacheValue;
            }
        }

        config()->set('cache.prefix', $prefix);
        return $events;
    }


}