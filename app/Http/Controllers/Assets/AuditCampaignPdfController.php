<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetAuditCampaign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuditCampaignPdfController extends Controller
{
    public function __invoke(string $uuid): Response
    {
        Gate::authorize('audit', Asset::class);

        $campaign = AssetAuditCampaign::query()
            ->with(['items.asset', 'issues', 'unit', 'sector', 'financialBlock', 'createdBy', 'finishedBy'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $total = $campaign->items->count();
        $done = $campaign->items->where('status', '!=', 'PENDING')->count();
        $openIssues = $campaign->issues->where('status', 'OPEN')->count();
        $conformity = $total > 0 ? (int) round(($done - $openIssues) * 100 / $total) : 0;

        $pdf = Pdf::loadView('pdf.assets.audit-campaign-report', [
            'campaign' => $campaign,
            'metrics' => [
                'total' => $total,
                'done' => $done,
                'pending' => max(0, $total - $done),
                'openIssues' => $openIssues,
                'conformity' => max(0, min(100, $conformity)),
            ],
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('auditoria-campanha-'.$campaign->id.'.pdf');
    }
}

