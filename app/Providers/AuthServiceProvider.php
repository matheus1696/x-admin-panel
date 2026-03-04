<?php

namespace App\Providers;

use App\Models\Assets\Asset;
use App\Policies\Assets\AssetPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Asset::class => AssetPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
