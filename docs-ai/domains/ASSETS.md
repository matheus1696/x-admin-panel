# Assets

## Responsabilidade

`Assets` governa o ciclo de vida do patrimonio operacional:

- entrada por nota fiscal
- composicao de estoque
- liberacao para unidade/setor
- historico de movimentacao e auditoria
- relatorios consolidados

## Entidades Principais

- `AssetInvoice`
- `AssetInvoiceItem`
- `Asset`
- `AssetEvent`
- `AssetReleaseOrder`
- `AssetReleaseOrderItem`
- `AssetAuditCampaign`
- `AssetAuditCampaignItem`
- `AssetAuditIssue`

## Permissoes Atuais

- `assets.view`
- `assets.invoices.manage`
- `assets.transfer`
- `assets.audit`
- `assets.state.change`
- `assets.return`
- `assets.reports.view`

## Rotas-Chave

- `assets.stock.index` (`/ativos/estoque`)
- `assets.invoices.index` (`/ativos/estoque/notas`)
- `assets.release-orders.*` (`/ativos/estoque/liberacoes/*`)
- `assets.index` (`/ativos/lista`)
- `assets.items.global` (`/ativos/lista/item-global`)
- `assets.show` (`/ativos/item/{uuid}`)
- `assets.audit-mobile` (`/ativos/auditoria-mobile`) -> auditoria individual
- `assets.audits.campaigns.*` (`/ativos/auditorias/campanhas/*`)
- `assets.reports.*` (`/ativos/relatorios/*`)

## Fluxos Criticos

### Entrada de ativos (nota fiscal)

1. cadastrar/editar nota em `InvoiceIndex` (modal)
2. cadastrar itens da nota
3. finalizar cadastro da nota
4. na finalizacao, criar ativos em `IN_STOCK` para o saldo pendente dos itens
5. bloquear edicao de itens e dados principais apos finalizacao

### Estoque separado da lista operacional

- `Assets Stock` mostra somente `IN_STOCK`
- `Assets List` nao exibe ativos em estoque
- liberacao remove o ativo do estoque (mudanca de estado/localizacao)

### Liberacao de ativos

- liberacao unitario no estoque (acao direta)
- liberacao em lote por pedido (`ReleaseOrder`)
- geracao de folha de rosto e PDF do pedido

### Auditoria

- auditoria individual (busca por codigo + foto)
- campanhas de auditoria (amostragem/execucao/finalizacao + PDF)

## Invariantes / Regras

- evento de ativo e append-only (sem update/delete)
- mudanca de estado/unidade/setor deve registrar `AssetEvent`
- setor deve pertencer a unidade quando informado
- finalizacao de nota requer item e valor total > 0
- nota finalizada nao permite editar/remover itens
- alteracoes de dominio devem passar por services

## Integracoes

- `Administration`: usuarios, permissao e fornecedores/produtos
- `Configuration`: unidade, setor e bloco financeiro
- `Audit` (dominio): rastreabilidade de operacoes e relatorios

## Riscos / Armadilhas

- duplicar regra de estoque entre UI e service
- liberar/transferir ativo fora de service transacional
- quebrar separacao entre estoque e lista operacional
- permitir edicao de nota apos finalizacao
- acoplar estado de auditoria a componente sem persistencia consistente

