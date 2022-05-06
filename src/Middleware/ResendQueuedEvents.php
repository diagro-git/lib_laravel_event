<?php
namespace Diagro\Events\Middleware;

use Closure;
use Diagro\Events\Jobs\ResendEvents;
use Illuminate\Http\Request;
use Diagro\Events\Cache\Event as EventCacher;

/**
 * When the user has queued events, resend them.
 * This middleware is bound to the group "broadcast".
 *
 * @package Diagro\Events\Middleware
 */
class ResendQueuedEvents
{


    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if($response->isOk() && auth()->check()) {
            $companyId = auth()->user()->company()->id();
            $userId = auth()->user()->id();
            $events = EventCacher::getCachedEvents($companyId, $userId);
            if(count($events) > 0) {
                ResendEvents::dispatch($events, $companyId, $userId);
            }
        }

        return $response;
    }


}
