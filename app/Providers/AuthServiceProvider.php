<?php

namespace App\Providers;

use App\Models\Assets\Asset;
use App\Models\Process\Process;
use App\Models\TimeClock\TimeClockEntry;
use App\Models\TimeClock\TimeClockLocation;
use App\Policies\Assets\AssetPolicy;
use App\Policies\Process\ProcessPolicy;
use App\Policies\TimeClock\TimeClockEntryPolicy;
use App\Policies\TimeClock\TimeClockLocationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Asset::class => AssetPolicy::class,
        Process::class => ProcessPolicy::class,
        TimeClockEntry::class => TimeClockEntryPolicy::class,
        TimeClockLocation::class => TimeClockLocationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
