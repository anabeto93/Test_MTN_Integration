<?php

namespace App\Repositories\Mtn;

interface MtnRepositoryContract 
{
    public function transact($transaction);
}
