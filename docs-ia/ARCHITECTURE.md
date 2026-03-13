# Architecture

Visao consolidada da arquitetura atual do X-AdminPanel.
Atualizado em: 2026-03-12.

## Visao Geral

X-AdminPanel e uma aplicacao Laravel 12 + Livewire 3 organizada por dominio.

Dominios principais:

- `Organization`: estrutura institucional, organograma e templates de workflow
- `Process`: processos administrativos guiados por workflow e despacho
- `Task`: execucao operacional por hubs, tarefas e etapas
- `Assets`: patrimonio operacional, estoque, liberacoes e auditoria
- `TimeClock`: registro de ponto, locais de batida e relatorios
- `Administration`: usuarios, RBAC, produtos, fornecedores e catalogos operacionais
- `Configuration`: referencias base compartilhadas
- `Audit`, `Auth`, `Profile`, `Public`, `Dashboard`: borda, suporte e observabilidade

## Modelo De Camadas

Fluxo esperado:

`Services -> Controllers/Livewire -> Blade`

- `Services`: regras de negocio, consistencia, transacao e escrita de dominio
- `Controllers`: fluxos HTTP classicos e paginas Blade tradicionais
- `Livewire`: estado de tela, entrada do usuario e orquestracao de interface
- `Blade`: renderizacao declarativa

## Eixos Do Sistema

- Eixo estrutural: `Organization`
- Eixo de processos administrativos: `Process`
- Eixo de execucao operacional: `Task`
- Eixo patrimonial: `Assets`

`Organization` alimenta `Process` e `Task` com contexto estrutural.
`Administration` e `Configuration` sustentam os catalogos e referencias desses modulos.

## Dependencias Reais Entre Modulos

### Organization -> Process

- `Workflow` e `WorkflowStep` servem como template para `Process` e `ProcessStep`
- `organization_id` da etapa corrente define o setor responsavel pelo despacho
- usuarios vinculados ao setor atual controlam as acoes criticas do processo

### Organization -> Task

- `WorkflowStep.organization_id` pode ser copiado para `TaskStep.organization_id`
- integridade de ordem e prazo precisa ser preservada na copia

### Task -> Administration

- tasks dependem de usuarios, status, categorias e prioridades
- catalogos de task sao contextualizados por hub
- permissao entra pela borda e pode ser reforcada no contexto do hub

### Assets -> Administration / Configuration

- depende de usuarios, fornecedores, produtos e unidades de medida
- depende de estabelecimento, setor e bloco financeiro
- mutacoes criticas ficam em `InvoiceService`, `AssetOperationService`, `ReleaseOrderService`, `AuditService` e `AuditCampaignService`

### TimeClock -> Administration / Policies

- registros pertencem a `User`
- autorizacao contextual usa policies para registro, listagem, gestao de locais e relatorios
- regras de validacao operacional ficam no service e em validadores dedicados

### Dashboard -> Task / Process / Notification

- dashboard agrega visoes leves de tarefas, processos e notificacoes
- nao e dono de regra de negocio

## Estado Atual Relevante

- `TaskAside` e `TaskStepAside` ja escrevem via `TaskService`
- `TaskPage` ainda concentra parte da orquestracao do modulo e continua sendo acoplamento conhecido
- `ProcessService` ja cobre abertura, despacho, atribuicao, visibilidade, notificacoes e indicadores
- `TimeClock` ja esta ativo com services, policies, validadores e relatorios
- notificacoes de sistema sao transversais e hoje aparecem principalmente em processos e dashboard

## Regras Estruturais

- hierarquia do organograma nao pode gerar ciclo
- qualquer mutacao estrutural exige `OrganizationChartService::reorder()`
- traversal de hierarquia nao deve ser duplicado fora do service central
- regras de dominio nao devem migrar para Blade
- modulo de borda nao deve virar concentrador de negocio

## Zonas De Maior Risco

- Maximo: `Organization`
- Alto: `Process`, `Task`
- Medio: `Assets`, `TimeClock`, `Administration`, `Configuration`
- Suporte: `Audit`, `Auth`, `Profile`, `Public`, `Dashboard`

## Leitura Recomendada

1. `SYSTEM_GUIDE.md`
2. `domains/ORGANIZATION.md`
3. `domains/PROCESS.md`
4. `domains/TASK.md`
5. `domains/ASSETS.md`
6. `domains/TIME_CLOCK.md`
