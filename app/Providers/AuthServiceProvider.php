<?php

namespace App\Providers;

use App\Models\Assets\Asset;
use App\Models\Process\Process;
use App\Policies\Assets\AssetPolicy;
use App\Policies\Process\ProcessPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Asset::class => AssetPolicy::class,
        Process::class => ProcessPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
