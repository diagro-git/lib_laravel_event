<?php
namespace Diagro\Events\Occupied;

class Factory
{


    public static function getOccupied(?string $broadcastConnection = null): ?Contract
    {
        $broadcastConnection ??= config('broadcasting.default');
        return match ($broadcastConnection) {
            default => null,
            'pusher' => new Pusher(),
        };
    }


}