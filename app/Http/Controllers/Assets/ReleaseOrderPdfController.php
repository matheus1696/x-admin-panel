<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetReleaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReleaseOrderPdfController extends Controller
{
    public function __invoke(string $uuid): Response
    {
        Gate::authorize('transfer', Asset::class);

        $order = AssetReleaseOrder::query()
            ->with(['items', 'toUnit', 'toSector', 'releasedBy'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.assets.release-order-cover', [
            'order' => $order,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('folha-liberacao-'.$order->code.'.pdf');
    }
}

