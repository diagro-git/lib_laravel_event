<?php
namespace Diagro\Events\Occupied;

interface Contract
{

    public function isOccupied(string $channelName): string;

}