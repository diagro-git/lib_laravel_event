<?php
namespace Diagro\Events\Jobs;

use Diagro\Events\Cache\Event as EventCacher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ResendEvents implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable;


    public function __construct(
        public array $events,
        public $userId
    )
    {
        $this->onQueue('events_resend');
    }


    public function handle()
    {
        foreach($this->events as $event) {
            if($event instanceof ShouldBroadcast) {
                event($event);
            }
        }

        //any events left?
        $events = EventCacher::getCachedEvents($this->userId);
        if(count($events) > 0) {
            //resend failed events
            ResendEvents::dispatch($events, $this->userId)->delay(1);
        }
    }

}