<?php

namespace App\Validation\TimeClock;

use App\Models\TimeClock\TimeClockLocation;

class LocationWithinRadiusValidator
{
    public function validateOrFail(?float $latitude, ?float $longitude, ?int $locationId): void
    {
        if (! config('time_clock.validate_location_enabled') || ! $locationId || $latitude === null || $longitude === null) {
            return;
        }

        $location = TimeClockLocation::query()
            ->whereKey($locationId)
            ->where('active', true)
            ->first();

        if (! $location) {
            return;
        }

        $distance = $this->distanceInMeters(
            $latitude,
            $longitude,
            (float) $location->latitude,
            (float) $location->longitude,
        );

        if ($distance > (int) $location->radius_meters) {
            throw new TimeClockValidationException('outside_radius', 'Voce esta fora do raio permitido para este local.');
        }
    }

    private function distanceInMeters(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($fromLat);
        $lngFrom = deg2rad($fromLng);
        $latTo = deg2rad($toLat);
        $lngTo = deg2rad($toLng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
}
