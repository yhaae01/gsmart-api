<?php

namespace App\Policies;

use App\Models\Prospect;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class ProspectPolicy
{
    use HandlesAuthorization;

    public function pickUpSales(User $user, Prospect $prospect)
    {
        if (!$user->hasRole('AMS')) {
            return true;
        }

        return ($user->ams->id == $prospect->amsCustomer->ams_id);
    }
}
