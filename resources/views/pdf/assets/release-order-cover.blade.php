<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Folha de liberacao {{ $order->code }}</title>
    <style>
        @page {
            margin: 110px 24px 80px 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            margin: 0;
        }

        .pdf-header {
            position: fixed;
            top: -92px;
            left: 0;
            right: 0;
            height: 72px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 10px;
        }

        .pdf-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pdf-header-table td {
            vertical-align: middle;
        }

        .logo {
            height: 38px;
        }

        .system-name {
            text-align: right;
            font-size: 13px;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 2px;
        }

        .system-info {
            text-align: right;
            font-size: 10px;
            color: #4b5563;
        }

        .pdf-footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 48px;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            font-size: 10px;
            color: #6b7280;
        }

        .pdf-footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-right {
            text-align: right;
        }

        .page-counter:after {
            content: counter(page) " / " counter(pages);
        }

        .title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .subtitle {
            color: #4b5563;
            margin-bottom: 16px;
        }

        .code-box {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 14px;
        }

        .code {
            font-size: 18px;
            font-weight: 700;
            margin-top: 4px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .grid td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            vertical-align: top;
        }

        .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .value {
            font-weight: 600;
        }

        .section-title {
            margin-top: 16px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #374151;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.items th {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            text-align: left;
            padding: 7px;
            font-size: 11px;
        }

        table.items td {
            border: 1px solid #e5e7eb;
            padding: 7px;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .signatures {
            width: 100%;
            margin-top: 24px;
        }

        .signature-box {
            width: 46%;
            display: inline-block;
            vertical-align: top;
            margin-right: 4%;
        }

        .signature-box:last-child {
            margin-right: 0;
        }

        .line {
            border-bottom: 1px solid #6b7280;
            height: 52px;
            margin-bottom: 6px;
        }

        .signature-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <table class="pdf-header-table">
            <tr>
                <td>
                    <img class="logo" src="{{ public_path('asset/img/logo_full.png') }}" alt="X-AdminPanel">
                </td>
                <td>
                    <div class="system-name">X-AdminPanel</div>
                    <div class="system-info">Controle de Ativos • Folha de Rosto de Liberacao</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td>Documento gerado em {{ now()->format('d/m/Y H:i') }}</td>
                <td class="text-right">Pagina <span class="page-counter"></span></td>
            </tr>
        </table>
    </div>

    <div class="title">Folha de rosto da liberacao</div>
    <div class="subtitle">Documento para conferencia e assinatura do recebimento dos ativos</div>

    <div class="code-box">
        <div class="label">Pedido de liberacao</div>
        <div class="code">{{ $order->code }}</div>
    </div>

    <table class="grid">
        <tr>
            <td>
                <div class="label">Data/Hora da liberacao</div>
                <div class="value">{{ optional($order->released_at)->format('d/m/Y H:i') ?: '-' }}</div>
            </td>
            <td>
                <div class="label">Destino</div>
                <div class="value">{{ $order->toUnit?->title ?: '-' }}</div>
                <div>{{ $order->toSector?->title ?: 'Sem setor' }}</div>
            </td>
            <td>
                <div class="label">Responsavel pela liberacao</div>
                <div class="value">{{ $order->releasedBy?->name ?: '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Solicitante</div>
                <div class="value">{{ $order->requester_name ?: '-' }}</div>
            </td>
            <td>
                <div class="label">Recebedor</div>
                <div class="value">{{ $order->receiver_name ?: '-' }}</div>
            </td>
            <td>
                <div class="label">Total de ativos</div>
                <div class="value">{{ $order->total_assets }}</div>
            </td>
        </tr>
    </table>

    @if ($order->notes)
        <table class="grid">
            <tr>
                <td>
                    <div class="label">Observacoes</div>
                    <div>{{ $order->notes }}</div>
                </td>
            </tr>
        </table>
    @endif

    <div class="section-title">Itens liberados</div>
    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th>Codigo</th>
                <th>Patrimonio</th>
                <th>Nota</th>
                <th class="text-center">Bloco</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->items as $item)
                <tr>
                    <td>{{ $item->item_description }}</td>
                    <td>{{ $item->asset_code }}</td>
                    <td>{{ $item->patrimony_number ?: '-' }}</td>
                    <td>{{ $item->invoice_number ?: '-' }}</td>
                    <td class="text-center">{{ $item->financial_block_label ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Nenhum item registrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Assinaturas</div>
    <div class="signatures">
        <div class="signature-box">
            <div class="line"></div>
            <div class="signature-label">Assinatura de quem entrega</div>
        </div>
        <div class="signature-box">
            <div class="line"></div>
            <div class="signature-label">Assinatura de quem recebe</div>
        </div>
    </div>
</body>
</html>
