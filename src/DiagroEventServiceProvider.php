<?php
namespace Diagro\Events;

use Diagro\Events\Middleware\ResendQueuedEvents;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Bridge between package and laravel application.
 *
 * @package Diagro\Backend
 */
class DiagroEventServiceProvider extends ServiceProvider
{


    /**
     * Boot me up Scotty!
     */
    public function boot(Kernel $kernel)
    {
        //middleware
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('broadcast', ResendQueuedEvents::class);

        //commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                //BackendTokenGenerator::class,
            ]);
        }
    }


}