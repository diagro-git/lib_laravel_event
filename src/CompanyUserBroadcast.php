<?php
namespace Diagro\Events;

abstract class CompanyUserBroadcast
{

    use BroadcastWhenOccupied;


    public function __construct(?int $company_id = null, ?int $user_id = null)
    {
        if($company_id == null) $company_id = auth()->user()->company()->id();
        if($user_id == null) $user_id = auth()->user()->id();

        $this->company_id = $company_id;
        $this->user_id = $user_id;
    }


}