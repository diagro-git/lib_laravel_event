<?php
namespace Diagro\Events\Occupied;

class Pusher implements Contract
{


    public function isOccupied(string $channelName): string
    {
        $pusher = new \Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), ['cluster' => env('PUSHER_APP_CLUSTER')]);
        $info = $pusher->getChannelInfo($channelName);
        return $info->occupied;
    }

}