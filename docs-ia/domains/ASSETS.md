# Assets

## Responsabilidade

`Assets` governa o ciclo de vida do patrimonio operacional:

- entrada por nota fiscal
- composicao de estoque
- liberacao para unidade e setor
- retorno ao patrimonio
- historico de movimentacao
- auditoria individual e por campanha
- relatorios consolidados

## Entidades E Services Principais

- `AssetInvoice`
- `AssetInvoiceItem`
- `Asset`
- `AssetEvent`
- `AssetReleaseOrder`
- `AssetReleaseOrderItem`
- `AssetAuditCampaign`
- `AssetAuditCampaignItem`
- `AssetAuditIssue`
- `InvoiceService`
- `AssetOperationService`
- `ReleaseOrderService`
- `AuditService`
- `AuditCampaignService`
- `AssetsReportService`

## Rotas-Chave

- `assets.stock.index`
- `assets.invoices.index`
- `assets.release-orders.index|create|show|pdf`
- `assets.index`
- `assets.items.global`
- `assets.show`
- `assets.audit-mobile`
- `assets.audits.campaigns.index|create|show|pdf`
- `assets.reports.*`

## Fluxos Criticos

### Entrada por nota fiscal

1. cadastrar ou editar nota
2. cadastrar itens
3. finalizar nota
4. gerar ativos pendentes em `IN_STOCK`
5. bloquear edicao posterior

### Estoque separado da lista operacional

- `Assets Stock` mostra somente `IN_STOCK`
- `Assets List` nao exibe ativos em estoque
- liberacao altera estado e localizacao

### Liberacao e retorno

- liberacao unitaria usa `AssetOperationService::releaseFromStock()`
- liberacao em lote usa `ReleaseOrderService::createAndRelease()`
- transferencia operacional usa `AssetOperationService::transferAsset()`
- retorno ao patrimonio usa `AssetOperationService::returnToPatrimony()`

### Auditoria

- auditoria individual por codigo e foto
- campanhas de auditoria com execucao e PDF

## Invariantes

- `AssetEvent` e append-only
- mudanca de estado, unidade ou setor registra evento
- setor deve pertencer a unidade quando informado
- nota finalizada nao permite editar cabecalho nem itens
- ativos em estoque nao entram na lista operacional
- alteracoes criticas passam por services transacionais
- transferencia e retorno possuem protecoes de idempotencia

## Integracoes

- `Administration`: usuarios, permissao, fornecedores, produtos e unidades de medida
- `Configuration`: unidade, setor, estabelecimento e bloco financeiro
- `Audit`: trilha institucional complementar

## Riscos

- duplicar regra de estoque entre UI e service
- liberar ou transferir ativo fora de service transacional
- quebrar separacao entre estoque e lista operacional
- permitir edicao de nota apos finalizacao
- acoplar estado de auditoria a componente sem persistencia consistente
