<?php

namespace App\Services\Assets;

use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetEvent;
use App\Models\Assets\AssetInvoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetsReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function assetsByUnit(array $filters = []): Collection
    {
        return Asset::query()
            ->leftJoin('establishments', 'establishments.id', '=', 'assets.unit_id')
            ->selectRaw('COALESCE(establishments.title, ?) as unit_title', ['Sem unidade'])
            ->selectRaw('COUNT(*) as total_assets')
            ->selectRaw("SUM(CASE WHEN assets.state = ? THEN 1 ELSE 0 END) as in_stock_count", [AssetState::IN_STOCK->value])
            ->selectRaw("SUM(CASE WHEN assets.state = ? THEN 1 ELSE 0 END) as in_use_count", [AssetState::IN_USE->value])
            ->selectRaw("SUM(CASE WHEN assets.state = ? THEN 1 ELSE 0 END) as maintenance_count", [AssetState::MAINTENANCE->value])
            ->selectRaw("SUM(CASE WHEN assets.state = ? THEN 1 ELSE 0 END) as unserviceable_count", [AssetState::DAMAGED->value])
            ->groupBy('establishments.title')
            ->orderBy('unit_title')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, array{state: string, total: int}>
     */
    public function assetsByState(array $filters = []): Collection
    {
        return Asset::query()
            ->selectRaw('state, COUNT(*) as total')
            ->groupBy('state')
            ->orderBy('state')
            ->get()
            ->map(fn ($row): array => [
                'state' => $row->state instanceof AssetState ? $row->state->value : (string) $row->state,
                'total' => (int) $row->total,
            ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function transfersByPeriod(array $filters = []): Collection
    {
        return $this->eventsByPeriod(AssetEventType::TRANSFERRED, $filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function auditsByPeriod(array $filters = []): Collection
    {
        return $this->eventsByPeriod(AssetEventType::AUDITED, $filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function purchasesByPeriod(array $filters = []): Collection
    {
        return AssetInvoice::query()
            ->withCount('items')
            ->when($filters['startDate'] ?? null, fn (Builder $query, $date) => $query->whereDate('issue_date', '>=', $date))
            ->when($filters['endDate'] ?? null, fn (Builder $query, $date) => $query->whereDate('issue_date', '<=', $date))
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, scalar|null>>  $rows
     */
    public function exportCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, $headers, ';');

            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function eventsByPeriod(AssetEventType $type, array $filters = []): Collection
    {
        return AssetEvent::query()
            ->where('type', $type->value)
            ->when($filters['startDate'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['endDate'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->selectRaw('DATE(created_at) as event_date, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) desc')
            ->get();
    }
}
