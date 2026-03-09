<?php

namespace App\Policies\Assets;

use App\Models\Administration\User\User;
use App\Models\Assets\Asset;

class AssetPolicy
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
        return $user->can('assets.view');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->can('assets.view');
    }

    public function manageInvoices(User $user): bool
    {
        return $user->can('assets.invoices.manage');
    }

    public function transfer(User $user): bool
    {
        return $user->can('assets.transfer');
    }

    public function audit(User $user): bool
    {
        return $user->can('assets.audit');
    }

    public function changeState(User $user): bool
    {
        return $user->can('assets.state.change');
    }

    public function returnToPatrimony(User $user): bool
    {
        return $user->can('assets.return');
    }

    public function viewReports(User $user): bool
    {
        return $user->can('assets.reports.view');
    }
}
