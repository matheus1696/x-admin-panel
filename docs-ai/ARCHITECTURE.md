# Architecture

## Visao Geral

X-AdminPanel e uma aplicacao Laravel 12 + Livewire 3 organizada por dominio.

- `Organization`: estrutura (organograma e workflow)
- `Task`: execucao (hubs, tarefas, etapas, kanban)
- `Administration`: identidade, permissoes e catalogos administrativos
- `Configuration`: cadastros base
- `Audit/Auth/Profile/Public/Dashboard`: dominios de borda e suporte

O eixo estrutural e `Organization`. O eixo operacional e `Task`.

## Modelo De Camadas

Fluxo esperado:

`Services -> Controllers/Livewire -> Blade`

- `Services`: regras de negocio, consistencia e escrita em dados
- `Controllers`: fluxos HTTP classicos
- `Livewire`: estado de tela, validacao de entrada e orquestracao
- `Blade`: camada declarativa

## Estado Atual Relevante

- Escritas operacionais de `TaskAside` e `TaskStepAside` foram consolidadas em `TaskService`.
- `TaskPage` ainda concentra orquestracao importante do modulo (realidade atual), mas os fluxos criticos de escrita avancaram para service.
- Status e categorias de task sao contextualizados por hub (`task_hub_id`).

## Fluxos Criticos Entre Modulos

### Organization -> Task

- `WorkflowStep.organization_id` e `TaskStep.organization_id` conectam estrutura com execucao.
- Copia de workflow para task deve preservar ordem e coerencia de prazo.

### Task -> Administration

- `Task` depende de usuarios, permissoes e catalogos (status/prioridade/categorias).
- Permissoes entram na borda por middleware/policy e sao reforcadas no contexto quando necessario.

### Profile/Auth -> Administration

- `User` e a identidade central.
- Fluxos legados de profile (`/profile`) estao mantidos para compatibilidade de testes e tooling.

## Regras Estruturais

- Organograma nao pode gerar ciclo.
- Reordenacao estrutural e obrigatoria apos mutacao de hierarquia.
- Regras de dominio nao devem ser movidas para Blade.
- Evitar duplicacao de traversal de hierarquia fora de `OrganizationChartService`.

## Risco Arquitetural

- Maximo: `Organization`
- Alto: `Task`
- Medio: `Administration`, `Auth`, `Profile`, `Configuration`
- Suporte: `Audit`, `Dashboard`, `Public`

## Leitura Recomendada

1. `SYSTEM_GUIDE.md`
2. `domains/ORGANIZATION.md`
3. `domains/TASK.md`
4. `domains/ADMINISTRATION.md`
