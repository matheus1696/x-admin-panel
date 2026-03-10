<?php

namespace App\Services\TimeClock;

use App\Models\Administration\User\User;
use App\Models\TimeClock\TimeClockEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TimeClockReportService
{
    public function entriesByPeriod(array $filters = []): Collection
    {
        return TimeClockEntry::query()
            ->with(['user', 'location'])
            ->when($filters['dateFrom'] ?? null, fn (Builder $query, $date) => $query->whereDate('occurred_at', '>=', $date))
            ->when($filters['dateTo'] ?? null, fn (Builder $query, $date) => $query->whereDate('occurred_at', '<=', $date))
            ->when($filters['userId'] ?? null, fn (Builder $query, $userId) => $query->where('user_id', (int) $userId))
            ->when(($filters['status'] ?? 'all') !== 'all', fn (Builder $query) => $query->where('status', $filters['status']))
            ->orderByDesc('occurred_at')
            ->get();
    }

    public function usersWithoutEntryToday(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->whereDoesntHave('timeClockEntries', fn (Builder $query) => $query->whereDate('occurred_at', today()))
            ->orderBy('name')
            ->get();
    }

    public function exportEntriesCsv(array $filters = []): StreamedResponse
    {
        $rows = $this->entriesByPeriod($filters)
            ->map(fn (TimeClockEntry $entry) => [
                $entry->user?->name,
                optional($entry->occurred_at)->format('d/m/Y H:i:s'),
                $entry->status,
                $entry->latitude,
                $entry->longitude,
                $entry->accuracy,
                $entry->location?->name,
            ])->all();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Usuario', 'Data/Hora', 'Status', 'Latitude', 'Longitude', 'Precisao', 'Local'], ';');

            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, 'time-clock-entries.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
