<?php

namespace App\Services\TimeClock;

use App\Models\TimeClock\TimeClockLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimeClockLocationService
{
    public function list(): Collection
    {
        return TimeClockLocation::query()
            ->orderByDesc('active')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): TimeClockLocation
    {
        return DB::transaction(fn () => TimeClockLocation::query()->create($data));
    }

    public function update(TimeClockLocation $location, array $data): TimeClockLocation
    {
        return DB::transaction(function () use ($location, $data): TimeClockLocation {
            $location->update($data);

            return $location->refresh();
        });
    }
}
