<?php

namespace App\Policies\TimeClock;

use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockLocation;

class TimeClockLocationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function manage(User $user): bool
    {
        return $user->can('time_clock.locations.manage');
    }

    public function viewAny(User $user): bool
    {
        return $this->manage($user);
    }

    public function view(User $user, TimeClockLocation $location): bool
    {
        return $this->manage($user);
    }
}
