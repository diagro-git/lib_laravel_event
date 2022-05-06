<?php
namespace Diagro\Events;

abstract class UserCompanyBroadcast
{

    use BroadcastWhenOccupied;


    public function __construct(int $user_id, int $company_id)
    {
        $this->user_id = $company_id . '.' . $user_id;
    }


}