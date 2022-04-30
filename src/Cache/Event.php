<?php
namespace Diagro\Events\Cache;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Cache;

/**
 * Class for caching the events.
 */
class Event
{


    public static function getCacheKey(): string
    {
        return 'events.' . auth()->user()->getAuthIdentifier();
    }


    public static function putInCache(ShouldBroadcast $event)
    {
        $cacheKey = self::getCacheKey();
        $value = [$event];

        //merge with existence events in the cache
        if(Cache::has($cacheKey)) {
            $cachedValue = Cache::get($cacheKey);
            if(is_array($cachedValue)) {
                $value = array_merge(Cache::get($cacheKey), $value);
            }
        }

        Cache::put($cacheKey, $value);
    }


    public static function getCachedEvents(): array
    {
        $events = [];
        $cacheKey = self::getCacheKey();

        if(Cache::has($cacheKey)) {
            $cacheValue = Cache::pull($cacheKey);
            if(is_array($cacheValue)) {
                $events = $cacheValue;
            }
        }

        return $events;
    }


}