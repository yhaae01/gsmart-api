<?php

namespace App\Policies;

use App\Models\Sales;
use App\Models\Prospect;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class SalesPolicy
{
    use HandlesAuthorization;

    public function show(User $user, Sales $sales)
    {
        if (!$user->hasRole('AMS')) {
            return true;
        }

        return ($user->ams->id == $sales->ams_id);
    }

    public function pickUpSales(User $user, Prospect $prospect)
    {
        if (!$user->hasRole('AMS')) {
            return false;
        }

        return ($user->ams->id == $prospect->amsCustomer->ams_id);
    }

    public function deleteTmb(User $user, Sales $sales)
    {
        if (!$user->hasRole('AMS')) {
            return false;
        }

        if ($sales->transaction_type_id == 3) {
            return false;
        }

        return ($user->ams->id == $sales->ams_id);
    }
}
