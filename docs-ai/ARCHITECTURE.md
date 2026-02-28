# Architecture

## Visão Geral

O sistema é uma aplicação Laravel 12 com interface baseada em Livewire 3. O código está organizado por contexto de negócio:

- `Organization`: organograma e workflow
- `Task`: hubs, tarefas, etapas e histórico operacional
- `Administration`: usuários e catálogos usados por tasks
- `Configuration`: cadastros base
- `Audit`: histórico geral do sistema

O eixo estrutural é `Organization`. `OrganizationChart` alimenta `WorkflowStep.organization_id` e `TaskStep.organization_id`. Por isso, estrutura organizacional, modelagem de processo e execução não são módulos isolados.

## Camadas

### Services

Services concentram mutações e consistência:

- `OrganizationChartService` recalcula a hierarquia
- `WorkflowStepService` mantém ordem e `total_estimated_days`
- `TaskService` controla criação, métricas, kanban, status e histórico

Services administrativos e de configuração cobrem CRUD e filtros mais simples.

### Livewire

Livewire é a camada principal de interface:

- carrega contexto e coleções
- controla modais
- valida entrada
- chama services

`TaskPage` é a principal exceção: além de orquestrar UI, também concentra parte da regra de execução, como cópia de workflow, validação de motivo e criação direta de `TaskStep`.

### Models

Models definem relacionamentos e alguns campos derivados:

- `OrganizationChart` é autorreferenciado por `hierarchy`
- `Workflow` agrega `WorkflowStep`
- `TaskHub` agrega `Task` e `TaskStep`
- `Task` e `TaskStep` geram `code` no evento `created`

### Controllers E Rotas

Controllers ficam nos fluxos HTTP clássicos:

- auth
- profile
- dashboard
- audit

Rotas agrupam os contextos e aplicam autorização na borda com `can:`.

## Fluxo Entre Módulos

### Estrutura

`OrganizationChartConfigPage` grava setores via `OrganizationChartService`. Depois de criar ou alterar um nó, `reorder()` recalcula `order` e `number_hierarchy`. A renderização do organograma depende desse resultado.

### Processo

`WorkflowProcessesPage` mantém o cabeçalho do processo. `WorkflowSteps` e `WorkflowStepService` mantêm:

- `step_order`
- `deadline_days`
- `total_estimated_days`
- vínculo opcional com `OrganizationChart`

### Execução

`TaskHub` define o ambiente. `TaskPage` carrega o hub apenas para owner ou membro. A execução passa por `TaskService` para:

- criar tarefas
- montar listas, dashboard e kanban
- mover tarefas e etapas
- registrar histórico

Há um fluxo derivado importante: `TaskPage::copyWorkflowSteps()` transforma `WorkflowStep` em `TaskStep`, preserva `organization_id` e converte `deadline_days` em `deadline_at`.

### Governança E Apoio

`Administration` fornece:

- usuários
- permissões
- status, categorias e prioridades de tasks

`Configuration` fornece:

- estabelecimentos e departamentos
- regiões
- ocupações
- blocos financeiros

`Audit` registra eventos gerais em `ActivityLog`. O histórico operacional de execução continua em `TaskActivity` e `TaskStepActivity`.

## Leitura Rápida

Para entender impacto real:
1. leia `Organization`
2. depois `Task`
3. só então os módulos de suporte

O comportamento mais sensível está no encontro entre estrutura e execução.
