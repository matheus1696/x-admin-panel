# Assets Module Guide (Estado Atual)

Guia rapido para manutencao do modulo `Assets` no estado atual do projeto.

## 1. Escopo Atual

O modulo cobre:

- entrada de ativos por nota fiscal
- estoque separado da lista operacional
- liberacao de ativos (unitaria e em lote por pedido)
- auditoria individual e por campanha
- relatorios de ativos

## 2. Regra Central De Fluxo

Fluxo oficial:

`Nota Fiscal -> Entrada de Ativos -> Estoque de Ativos -> Liberacao -> Lista Operacional`

Regras:

- ativo recebido aparece em `Assets Stock` (`IN_STOCK`)
- ativo liberado sai do estoque
- ativos em estoque nao aparecem em `Assets List`
- finalizacao de nota bloqueia edicao de itens

## 3. Permissoes Em Uso

- `assets.view`
- `assets.invoices.manage`
- `assets.transfer`
- `assets.audit`
- `assets.state.change`
- `assets.return`
- `assets.reports.view`

Observacao:
- `assets.stock.receive` nao faz parte do fluxo atual.

## 4. Rotas Principais

- `assets.stock.index`
- `assets.invoices.index`
- `assets.release-orders.index|create|show|pdf`
- `assets.index`
- `assets.items.global`
- `assets.show`
- `assets.audit-mobile` (auditoria individual)
- `assets.audits.campaigns.index|create|show|pdf`
- `assets.reports.*`

## 5. Componentes Livewire Principais

- `AssetsStockIndex`
- `InvoiceIndex`
- `ReleaseOrderIndex`, `ReleaseOrderCreate`, `ReleaseOrderShow`
- `AssetsIndex`, `GlobalItemAssetsIndex`, `AssetShow`
- `AuditMobile`, `AuditCampaignIndex`, `AuditCampaignCreate`, `AuditCampaignShow`
- `AssetsByUnit`, `AssetsByState`, `TransfersByPeriod`, `AuditsByPeriod`, `PurchasesByPeriod`

## 6. Services Principais

- `InvoiceService`
- `AssetOperationService`
- `ReleaseOrderService`
- `AuditService`
- `AuditCampaignService`
- `AssetsReportService`

Regras:
- escrita critica deve permanecer em `Services`
- operacoes de mutacao devem usar transacao e lock quando necessario
- eventos de ativos devem permanecer append-only

## 7. Tela De Auditoria

No menu lateral:

- `Controle de Ativos -> Auditoria -> Ativos Operacionais`
- `Controle de Ativos -> Auditoria -> Campanhas de Auditoria`
- `Controle de Ativos -> Auditoria -> Auditoria Individual`

## 8. Checklist Antes De Alterar

1. A mudanca preserva separacao `stock` vs `assets list`?
2. Finalizacao de nota continua sendo o gatilho de entrada em estoque?
3. Liberacao continua removendo item de estoque?
4. Toda mudanca de estado/localizacao gera `AssetEvent`?
5. A regra foi implementada em service e nao em Blade?
6. Permissao de rota e de contexto estao coerentes?

## 9. Evolucao Recomendada

Quando ampliar o modulo, preferir:

- ampliar telas existentes (`InvoiceIndex`, `AssetsStockIndex`) em vez de recriar fluxo paralelo
- preservar fluxo de nota por modal/pagina atual, sem voltar ao modelo legado
- manter testes de `tests/Feature/Assets` como rede de seguranca primaria

