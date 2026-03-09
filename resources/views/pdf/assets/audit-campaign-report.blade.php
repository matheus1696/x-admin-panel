<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatorio de Auditoria - {{ $campaign->title }}</title>
    <style>
        @page { margin: 90px 24px 60px 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; margin: 0; }
        .header { position: fixed; top: -75px; left: 0; right: 0; border-bottom: 1px solid #d1d5db; padding-bottom: 8px; }
        .header table, .footer table, .meta, .items { width: 100%; border-collapse: collapse; }
        .logo { height: 32px; }
        .title { text-align: right; font-weight: 700; color: #065f46; }
        .footer { position: fixed; bottom: -48px; left: 0; right: 0; border-top: 1px solid #d1d5db; padding-top: 6px; color: #6b7280; font-size: 10px; }
        .text-right { text-align: right; }
        .counter:after { content: counter(page) " / " counter(pages); }
        .box { border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; margin-bottom: 10px; }
        .label { font-size: 9px; text-transform: uppercase; color: #6b7280; }
        .value { font-weight: 600; margin-top: 2px; }
        .meta td { border: 1px solid #e5e7eb; padding: 6px; vertical-align: top; }
        .section { margin: 10px 0 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #374151; }
        .items th { background: #f3f4f6; border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        .items td { border: 1px solid #e5e7eb; padding: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td><img class="logo" src="{{ public_path('asset/img/logo_full.png') }}" alt="X-AdminPanel"></td>
                <td class="title">X-AdminPanel • Relatorio de Auditoria</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td>Gerado em {{ now()->format('d/m/Y H:i') }}</td>
                <td class="text-right">Pagina <span class="counter"></span></td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="label">Campanha</div>
        <div class="value">{{ $campaign->title }}</div>
    </div>

    <table class="meta">
        <tr>
            <td><div class="label">Status</div><div class="value">{{ $campaign->status }}</div></td>
            <td><div class="label">Unidade</div><div class="value">{{ $campaign->unit?->title ?: 'Todas' }}</div></td>
            <td><div class="label">Setor</div><div class="value">{{ $campaign->sector?->title ?: 'Todos' }}</div></td>
            <td><div class="label">Bloco</div><div class="value">{{ $campaign->financialBlock?->acronym ?: '-' }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Total</div><div class="value">{{ $metrics['total'] }}</div></td>
            <td><div class="label">Concluidos</div><div class="value">{{ $metrics['done'] }}</div></td>
            <td><div class="label">Pendentes</div><div class="value">{{ $metrics['pending'] }}</div></td>
            <td><div class="label">Conformidade</div><div class="value">{{ $metrics['conformity'] }}%</div></td>
        </tr>
    </table>

    <div class="section">Pendencias abertas</div>
    <table class="items">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Ativo</th>
                <th>Descricao</th>
                <th>Observacoes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($campaign->issues->where('status', 'OPEN') as $issue)
                <tr>
                    <td>{{ $issue->issue_type }}</td>
                    <td>{{ $issue->asset?->code ?: '-' }}</td>
                    <td>{{ $issue->asset?->description ?: '-' }}</td>
                    <td>{{ $issue->notes ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sem pendencias abertas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

