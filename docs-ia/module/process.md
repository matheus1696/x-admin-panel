# Process Guide

Guia de manutencao do modulo `Process` no estado atual do projeto.
Atualizado em: 2026-03-12.

## Escopo Atual

O modulo cobre:

- abertura de processos
- vinculacao opcional a workflow
- geracao de etapas do processo
- despacho com avancar, retornar e comentar
- atribuicao de responsavel
- leitura analitica por dashboard
- notificacoes a usuarios envolvidos

## Arquitetura Atual

Camada principal:

- `ProcessService`: orquestra dominio, visibilidade, dashboard e notificacoes
- `ProcessEventService`: registra historico append-only

Interfaces:

- `ProcessIndexPage`
- `ProcessDashboardPage`
- `ProcessShowPage`

## Regras Que Nao Podem Ser Quebradas

- apenas usuarios do setor da etapa atual executam despacho
- comentario e obrigatorio em avancar e retornar
- processos fechados ou cancelados nao avancam fluxo
- owner ou usuarios dos setores vinculados podem visualizar
- deve existir uma unica etapa corrente

## Dependencias

- `Organization`: workflow e setores
- `Administration`: owner e usuarios vinculados aos setores
- `NotificationService`: avisos de interacao
- `Dashboard`: leitura resumida

## Checklist Antes De Alterar

1. O setor da etapa atual continua controlando a permissao contextual?
2. `organization_id` do processo continua refletindo a etapa corrente?
3. `ProcessEvent` continua sendo append-only e numerado em ordem?
4. As notificacoes continuam nascendo no service, nao na UI?
5. Os indicadores do dashboard continuam derivados de leitura, nao de estado paralelo?

## Expansao Segura

- adicionar novas acoes no `ProcessService`, nao na pagina Livewire
- manter `ProcessStatus` como fonte de estado do processo
- preferir enrich de dashboard via query e service em vez de estado duplicado
