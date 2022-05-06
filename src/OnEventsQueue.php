<?php
namespace Diagro\Events;

trait OnEventsQueue
{


    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @return string
     */
    public function broadcastQueue()
    {
        return 'events';
    }


}