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
     * The auth identifier
     *
     * @var mixed
     */
    public mixed $user_id;


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
            EventCacher::putInCache($this);
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
            $this->user_id = auth()->user()->getAuthIdentifier();
        }

        return new PresenceChannel($this->channelName() . '.' . $this->user_id);
    }


    /**
     * Check if the channel is occupied.
     *
     * @return bool
     */
    protected function isOccupied(): bool
    {
        return auth()->check() &&
            $this->getOccupied()?->isOccupied($this->broadcastOn()->name);
    }


}