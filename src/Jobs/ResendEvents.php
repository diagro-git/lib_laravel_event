<?php
namespace Diagro\Events\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ResendEvents implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable;


    public function __construct(
        public array $events
    )
    {
        $this->onQueue('resend_events');
    }


    public function handle()
    {
        foreach($this->events as $event) {
            if($event instanceof ShouldBroadcast) {
                event($event);
            }
        }
    }

}