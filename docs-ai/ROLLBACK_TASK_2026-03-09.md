# Rollback Task - 2026-03-09

Registro de pontos de restauracao da rodada atual no modulo `Task`.

## Etapa 1 - Base estrutural de estabilidade

- Ajuste de abertura/fechamento de asides com estado Livewire.
- Arquivos:
`app/Livewire/Task/TaskPage.php`
`resources/views/livewire/task/task-page.blade.php`
`resources/views/livewire/task/task-aside.blade.php`
`resources/views/livewire/task/task-step-aside.blade.php`

## Etapa 2 - Regras de fluxo para movimentacao de etapa

- Movimento de etapa bloqueado quando etapa anterior obrigatoria nao estiver concluida.
- Aplicado em kanban e alteracao direta de status.
- Arquivo:
`app/Services/Task/TaskService.php`

## Etapa 3 - Kanban com filtros

- Remocao do bloqueio de drag/drop com filtros ativos.
- Ordenacao recalculada com base completa para manter consistencia.
- Arquivo:
`app/Livewire/Task/TaskPage.php`

## Etapa 4 - UI de configuracoes e acesso

- Aba `Configuracoes` visivel apenas para proprietario.
- Inclusao de blocos de status de tarefa e status de etapa na aba.
- Arquivo:
`resources/views/livewire/task/task-page.blade.php`

## Etapa 5 - Kanban compacto

- Reducao de largura de colunas e cards de etapa.
- Remocao de bloco extra de prazo no card para deixar mais sucinto.
- Arquivo:
`resources/views/livewire/task/task-page.blade.php`

## Etapa 6 - Destaque visual no historico

- Destaque de cor para eventos de conclusao/cancelamento/reabertura.
- Arquivos:
`resources/views/livewire/task/task-aside.blade.php`
`resources/views/livewire/task/task-step-aside.blade.php`

## Etapa 7 - Atualizacao silenciosa a cada 30s

- Polling no `task-page` e nos asides.
- Arquivos:
`resources/views/livewire/task/task-page.blade.php`
`resources/views/livewire/task/task-aside.blade.php`
`resources/views/livewire/task/task-step-aside.blade.php`

## Testes atualizados

- `tests/Feature/Task/TaskPageTest.php`
- `tests/Feature/Task/TaskServiceTest.php`

## Nota de rollback

Para rollback parcial por etapa, usar este mapa para reverter apenas os arquivos listados na etapa desejada.
