<?php

namespace App\Policies\TimeClock;

use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockEntry;

class TimeClockEntryPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function register(User $user): bool
    {
        return $user->can('time_clock.register');
    }

    public function viewOwn(User $user): bool
    {
        return $user->can('time_clock.view_own');
    }

    public function viewAny(User $user): bool
    {
        return $user->can('time_clock.view_any');
    }

    public function view(User $user, TimeClockEntry $entry): bool
    {
        if ($user->can('time_clock.view_any')) {
            return true;
        }

        return $user->can('time_clock.view_own') && (int) $entry->user_id === (int) $user->id;
    }

    public function export(User $user): bool
    {
        return $user->can('time_clock.export');
    }

    public function viewReports(User $user): bool
    {
        return $user->can('time_clock.reports.view');
    }
}
