<?php

namespace App\Services\TimeClock;

use App\DTOs\TimeClock\RegisterTimeClockEntryDTO;
use App\Enums\TimeClock\TimeClockEntryStatus;
use App\Models\TimeClock\TimeClockEntry;
use App\Validation\TimeClock\GpsAccuracyValidator;
use App\Validation\TimeClock\GpsRequiredValidator;
use App\Validation\TimeClock\LocationWithinRadiusValidator;
use App\Validation\TimeClock\PhotoRequiredValidator;
use App\Validation\TimeClock\RegisterRateLimitValidator;
use App\Validation\TimeClock\TimeClockValidationException;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimeClockEntryService
{
    public function __construct(
        private readonly PhotoRequiredValidator $photoValidator,
        private readonly GpsRequiredValidator $gpsValidator,
        private readonly GpsAccuracyValidator $gpsAccuracyValidator,
        private readonly RegisterRateLimitValidator $rateLimitValidator,
        private readonly LocationWithinRadiusValidator $locationWithinRadiusValidator,
    ) {
    }

    public function register(RegisterTimeClockEntryDTO $dto): TimeClockEntry
    {
        return DB::transaction(function () use ($dto): TimeClockEntry {
            $this->rateLimitValidator->validateOrFail($dto->userId);

            $status = TimeClockEntryStatus::OK->value;
            $photoPath = null;

            try {
                $this->photoValidator->validateOrFail($dto->photo);
            } catch (TimeClockValidationException $exception) {
                $status = TimeClockEntryStatus::MISSING_PHOTO->value;
            }

            try {
                $this->gpsValidator->validateOrFail($dto->latitude, $dto->longitude, $dto->accuracy);
            } catch (TimeClockValidationException $exception) {
                if ($status === TimeClockEntryStatus::OK->value) {
                    $status = TimeClockEntryStatus::MISSING_GPS->value;
                }
            }

            try {
                $this->gpsAccuracyValidator->validateOrFail($dto->accuracy);
            } catch (TimeClockValidationException $exception) {
                if ($status === TimeClockEntryStatus::OK->value) {
                    $status = TimeClockEntryStatus::LOW_ACCURACY->value;
                }
            }

            $this->locationWithinRadiusValidator->validateOrFail($dto->latitude, $dto->longitude, $dto->locationId);

            if ($dto->photo) {
                $photoPath = $dto->photo->store('time-clock/entries/'.now()->format('Y/m'), 'public');
            }

            return TimeClockEntry::query()->create([
                'user_id' => $dto->userId,
                'occurred_at' => $dto->occurredAt,
                'photo_path' => $photoPath,
                'latitude' => $dto->latitude,
                'longitude' => $dto->longitude,
                'accuracy' => $dto->accuracy,
                'device_meta' => $dto->deviceMeta,
                'status' => $status,
                'location_id' => $dto->locationId,
            ]);
        });
    }

    public function paginateOwn(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->where('user_id', $userId)
            ->when($filters['dateFrom'] ?? null, fn (Builder $query, $date) => $query->whereDate('occurred_at', '>=', $date))
            ->when($filters['dateTo'] ?? null, fn (Builder $query, $date) => $query->whereDate('occurred_at', '<=', $date))
            ->paginate((int) ($filters['perPage'] ?? 10));
    }

    public function paginateAny(array $filters = []): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->when($filters['dateFrom'] ?? null, fn (Builder $query, $date) => $query->whereDate('occurred_at', '>=', $date))
            ->when($filters['dateTo'] ?? null, fn (Builder $query, $date) => $query->whereDate('occurred_at', '<=', $date))
            ->when($filters['userId'] ?? null, fn (Builder $query, $userId) => $query->where('user_id', (int) $userId))
            ->when(($filters['status'] ?? 'all') !== 'all', fn (Builder $query) => $query->where('status', $filters['status']))
            ->paginate((int) ($filters['perPage'] ?? 10));
    }

    public function monthlySummary(int $userId, ?CarbonImmutable $referenceDate = null): Collection
    {
        $referenceDate ??= CarbonImmutable::now();

        $startOfMonth = $referenceDate->startOfMonth();
        $endOfMonth = $referenceDate->endOfMonth();

        $entriesByDay = TimeClockEntry::query()
            ->where('user_id', $userId)
            ->whereBetween('occurred_at', [$startOfMonth, $endOfMonth])
            ->orderBy('occurred_at')
            ->get()
            ->groupBy(fn (TimeClockEntry $entry) => $entry->occurred_at?->format('Y-m-d'));

        return collect(range(1, $endOfMonth->day))
            ->map(function (int $day) use ($startOfMonth, $entriesByDay): array {
                $date = $startOfMonth->day($day);
                $entries = $entriesByDay->get($date->format('Y-m-d'), collect())->values();

                return [
                    'date' => $date,
                    'day_label' => $date->translatedFormat('d/m'),
                    'week_day' => ucfirst($date->translatedFormat('D')),
                    'morning_entry' => $this->formatEntryTime($entries->get(0)),
                    'morning_exit' => $this->formatEntryTime($entries->get(1)),
                    'afternoon_entry' => $this->formatEntryTime($entries->get(2)),
                    'afternoon_exit' => $this->formatEntryTime($entries->get(3)),
                    'activity_duration' => $this->formatDurationMinutes($this->calculateActivityDurationMinutes($entries)),
                    'observation' => null,
                ];
            });
    }

    public function findByUuid(string $uuid): TimeClockEntry
    {
        return $this->baseQuery()->where('uuid', $uuid)->firstOrFail();
    }

    private function baseQuery(): Builder
    {
        return TimeClockEntry::query()
            ->with(['user.organizations', 'location'])
            ->orderByDesc('occurred_at')
            ->orderByDesc('id');
    }

    private function formatEntryTime(?TimeClockEntry $entry): ?string
    {
        return $entry?->occurred_at?->format('H:i');
    }

    private function calculateActivityDurationMinutes(Collection $entries): int
    {
        $pairs = [
            [$entries->get(0), $entries->get(1)],
            [$entries->get(2), $entries->get(3)],
        ];

        return collect($pairs)
            ->sum(function (array $pair): int {
                [$start, $end] = $pair;

                if (! $start?->occurred_at || ! $end?->occurred_at) {
                    return 0;
                }

                return max(0, $start->occurred_at->diffInMinutes($end->occurred_at, false));
            });
    }

    private function formatDurationMinutes(int $minutes): ?string
    {
        if ($minutes <= 0) {
            return null;
        }

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
