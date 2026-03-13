# Assets Guide

Guia de manutencao do modulo `Assets` no estado atual do projeto.
Atualizado em: 2026-03-12.

## Escopo Atual

O modulo cobre:

- notas fiscais de entrada
- criacao de ativos em estoque
- liberacao unitaria e por pedido
- transferencia e retorno ao patrimonio
- auditoria individual
- campanhas de auditoria
- relatorios consolidados

## Fluxo Oficial

`Nota Fiscal -> Itens -> Finalizacao -> Ativos em Estoque -> Liberacao/Transferencia -> Lista Operacional`

Regras centrais:

- ativo recebido entra em `IN_STOCK`
- ativo em estoque nao aparece na lista operacional
- finalizacao de nota bloqueia edicao posterior
- toda mutacao relevante gera `AssetEvent`

## Services Que Nao Devem Ser Contornados

- `InvoiceService`
- `AssetOperationService`
- `ReleaseOrderService`
- `AuditService`
- `AuditCampaignService`
- `AssetsReportService`

## Telas Principais

- `AssetsStockIndex`
- `InvoiceIndex`
- `ReleaseOrderIndex`, `ReleaseOrderCreate`, `ReleaseOrderShow`
- `AssetsIndex`, `GlobalItemAssetsIndex`, `AssetShow`
- `AuditMobile`, `AuditCampaignIndex`, `AuditCampaignCreate`, `AuditCampaignShow`
- `AssetsByUnit`, `AssetsByState`, `TransfersByPeriod`, `AuditsByPeriod`, `PurchasesByPeriod`

## Checklist Antes De Alterar

1. A separacao `stock` x `lista operacional` continua preservada?
2. A finalizacao da nota continua sendo o gatilho de entrada?
3. Liberacao e retorno ainda passam por services transacionais?
4. Toda mudanca de estado ou localizacao continua registrando `AssetEvent`?
5. A regra depende de `Configuration` ou `Administration` e isso esta explicito?
6. A rota e a policy continuam coerentes?

## Expansao Segura

- ampliar telas existentes em vez de recriar fluxo paralelo
- manter finalizacao de nota como unica porta de entrada oficial
- preservar idempotencia de transferencia e retorno
- manter testes de `tests/Feature/Assets` como rede primaria de seguranca
