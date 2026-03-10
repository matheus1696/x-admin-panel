<?php

namespace App\Policies\Process;

use App\Models\Administration\User\User;
use App\Models\Process\Process;

class ProcessPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('process.view');
    }

    public function view(User $user, Process $process): bool
    {
        if ($user->can('process.manage')) {
            return true;
        }

        return $user->can('process.view')
            && ((int) $process->opened_by === (int) $user->id || (int) $process->owner_id === (int) $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('process.create');
    }

    public function manage(User $user): bool
    {
        return $user->can('process.manage');
    }

    public function close(User $user, Process $process): bool
    {
        return $user->can('process.close') || $user->can('process.manage');
    }

    public function viewTimeline(User $user, Process $process): bool
    {
        return $user->can('process.timeline.view') || $this->view($user, $process);
    }
}
