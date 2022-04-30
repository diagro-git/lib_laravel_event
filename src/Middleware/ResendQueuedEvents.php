<?php
namespace Diagro\Events\Middleware;

use Closure;
use Diagro\Events\Jobs\ResendEvents;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        return $next($request);
    }

    /**
     * If status is OK, then start the resend events job.
     *
     * @param $request
     * @param $response
     * @return void
     */
    public function terminate($request, $response)
    {
        if($response->isOk() && auth()->check()) {
            $events = EventCacher::getCachedEvents();
            if(count($events) > 0) {
                ResendEvents::dispatch($events);
            }
        }
    }


}
