<?php
namespace Diagro\Events\Cache;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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
        $cacheKey = self::getCacheKey($event->user_id);
        $value = ['event' => $event, 'time' => Carbon::now()->getTimestamp(), 'remove_at' => Carbon::now()->addSeconds($event->time_in_cache)->getTimestamp()];

        //merge with existence events in the cache
        if(Cache::has($cacheKey)) {
            $cachedValue = Cache::get($cacheKey);
            if(is_array($cachedValue)) {
                $value = array_merge($cachedValue, $value);
            }
        }

        Cache::put($cacheKey, $value);
    }


    public static function getCachedEvents($user_id): array
    {
        $events = [];
        $cacheKey = self::getCacheKey($user_id);

        if(Cache::has($cacheKey)) {
            $cacheValue = Cache::pull($cacheKey);
            if(is_array($cacheValue)) {
                $now = Carbon::now()->getTimestamp();
                foreach($cacheValue as $entry) {
                    $diff = $now - $entry['remove_at'];
                    if($diff > 0) {
                        $events[] = $entry['event'];
                    }
                }
                $events = $cacheValue;
            }
        }

        return $events;
    }


}