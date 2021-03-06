<?php
namespace Diagro\Events;

use Diagro\Events\Cache\Event as EventCacher;
use Diagro\Events\Occupied\Contract as OccupiedContract;
use Diagro\Events\Occupied\Factory;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Arr;

/**
 * Use this trait when having broadcast events
 * where the user needs to be connected first
 * to the channel before sending the event
 *
 * @see ShouldBroadcast
 */
trait BroadcastWhenOccupied
{

    /**
     * The user id
     *
     * @var mixed
     */
    public mixed $user_id;

    /**
     * The company id
     *
     * @var mixed
     */
    public mixed $company_id;

    /**
     * Counter how much this event was trying to send.
     *
     * @var int
     */
    public int $tries = 0;

    /**
     * Maximum tries before stop.
     *
     * @var int
     */
    public int $max_tries = 5;

    /**
     * Time this event should exist in cache for resending.
     *
     * @var int
     */
    public int $time_in_cache = 60;


    /**
     * The name of the channel.
     *
     * @return string
     */
    abstract protected function channelName(): string;


    /**
     * Get the instance for checking if channel is occupied.
     * Default behaviour is that the factory checks for the broadcasting driver.
     *
     * @return OccupiedContract|null
     */
    protected function getOccupied(): ?OccupiedContract
    {
        $broadcastConnection = null;
        if(method_exists($this, 'broadcastConnections')) {
            $broadcastConnection = Arr::first($this->broadcastConnections());
        }

        return Factory::getOccupied($broadcastConnection);
    }


    /**
     * Determine if this event should broadcast.
     * If the channel is not occupied, the event is cached to resend when the channel gets occupied.
     *
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        $isOccupied = $this->isOccupied();

        //cache the event, because this is not broadcasted
        if(! $isOccupied) {
            $this->tries += 1;
            if($this->tries < $this->max_tries) {
                EventCacher::putInCache($this);
            }
        }

        return $isOccupied;
    }


    /**
     * Get the presence channel instance.
     *
     * @return PresenceChannel
     */
    public function broadcastOn(): PresenceChannel
    {
        if(empty($this->user_id)) {
            $this->user_id = auth()->user()?->id();
        }

        if(empty($this->company_id)) {
            $this->company_id = auth()->user()?->company()->id();
        }

        return new PresenceChannel($this->channelName() . '.' . $this->company_id . '.' . $this->user_id);
    }


    /**
     * Check if the channel is occupied.
     *
     * @return bool
     */
    protected function isOccupied(): bool
    {
        return $this->getOccupied()?->isOccupied($this->broadcastOn()->name) ?? false;
    }


}