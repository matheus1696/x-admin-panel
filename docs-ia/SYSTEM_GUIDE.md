# SYSTEM GUIDE

Guia operacional consolidado para manutencao segura do X-AdminPanel.
Atualizado em: 2026-03-12.

## 1. Invariantes

### 1.1 Camadas

- fluxo obrigatorio: `Services -> Controllers/Livewire -> Blade`
- regra de negocio e consistencia ficam em `Services`
- `Livewire` gerencia estado, entrada e feedback
- `Blade` nao decide regra de dominio

### 1.2 Linguagem E Codificacao

- UI do usuario final: PT-BR
- arquivos e docs: UTF-8
- backend: Ingles

### 1.3 Hierarquia (`Organization`)

- `hierarchy = 0` representa raiz
- `hierarchy > 0` deve apontar para `organization_charts.id` valido
- proibido pai igual ao proprio no
- proibido ciclo
- mutacao estrutural exige `OrganizationChartService::reorder()`

### 1.4 Workflow (`Organization`)

- `WorkflowStep` sempre pertence a `Workflow`
- `step_order` precisa permanecer coerente no workflow
- `total_estimated_days` deve refletir a soma das etapas
- `organization_id`, quando informado, deve existir

### 1.5 Process

- `Process` visivel para owner ou usuario vinculado a um dos setores associados
- deve existir apenas uma etapa corrente por processo
- despacho de avancar e retornar exige comentario
- despacho operacional exige usuario pertencente ao setor da etapa atual
- processos `CLOSED` e `CANCELLED` nao podem continuar fluxo
- `ProcessEvent` e append-only

### 1.6 Task

- `Task` pertence a um `TaskHub`
- `TaskStep` pertence a `Task` e ao mesmo hub
- mover cards e steps preserva `kanban_order`
- transicoes terminais ajustam `finished_at`
- acesso ao hub somente por owner, membro ou compartilhamento valido

### 1.7 Catalogos De Task

- `task_statuses` e `task_step_statuses` sao por hub (`task_hub_id`)
- `task_categories` sao por hub
- `task_step_categories` permanecem catalogo de etapa

### 1.8 Assets

- `Asset` em estoque usa estado `IN_STOCK`
- ativos em estoque nao aparecem na lista operacional
- finalizacao de nota gera ativos pendentes em estoque
- nota finalizada bloqueia alteracao de itens e cabecalho
- mudanca de estado, unidade ou setor registra `AssetEvent`
- retorno ao patrimonio volta para a unidade configurada em `assets.patrimony_unit_id`

### 1.9 TimeClock

- registro passa por validacao de foto, GPS, rate limit e raio do local
- ausencia de foto ou GPS pode alterar o status final do registro
- local fora do raio invalida o registro
- `TimeClockEntry` e append-only
- relatorios leem registros consolidados; nao corrigem dominio

### 1.10 Auth / Profile

- `User` e a identidade autenticavel
- perfil atua sobre o usuario autenticado
- mudanca de email invalida verificacao (`email_verified_at = null`)
- rotas legadas `/profile` seguem mantidas por compatibilidade
- autorizacao de roles e permissoes usa Spatie Permission

## 2. Fluxos Criticos

### 2.1 Reordenacao Do Organograma

1. Validar pai e ciclo.
2. Persistir alteracao.
3. Executar `OrganizationChartService::reorder()`.
4. Abortar operacao se a estrutura ficar inconsistente.

### 2.2 Abertura De Processo

1. Definir titulo, owner e workflow opcional.
2. Se houver workflow, carregar etapas por `step_order`.
3. Criar `Process` em `IN_PROGRESS`.
4. Criar `ProcessStep` com uma etapa corrente.
5. Sincronizar setores relacionados.
6. Registrar eventos e notificacoes.

### 2.3 Despacho De Processo

1. Garantir que o ator pertence ao setor da etapa atual.
2. Validar comentario obrigatorio.
3. Atualizar etapa corrente e setor atual do processo.
4. Registrar `ProcessEvent`.
5. Notificar owner e usuarios dos setores relacionados.

### 2.4 Copia De Workflow Para Task

1. Validar tarefa sem etapas existentes ou fluxo aplicavel.
2. Carregar workflow ordenado por `step_order`.
3. Criar `TaskStep` preservando `organization_id`.
4. Calcular prazo acumulado e registrar historico.

### 2.5 Kanban De Task E Step

1. Validar origem e destino.
2. Aplicar regra de etapa obrigatoria anterior.
3. Atualizar status e ordem em transacao.
4. Ajustar datas terminais e motivo quando exigido.
5. Registrar atividade operacional.

### 2.6 Entrada E Liberacao De Ativos

1. Cadastrar nota e itens.
2. Finalizar nota apenas com itens validos.
3. Gerar ativos em estoque para saldo pendente.
4. Liberar unitariamente ou por pedido.
5. Registrar evento de movimentacao por ativo.

### 2.7 Registro De Ponto

1. Receber foto, coordenadas, precisao, data e local.
2. Aplicar rate limit do usuario.
3. Validar foto e GPS.
4. Validar se o ponto esta dentro do raio do local.
5. Persistir `TimeClockEntry` com status final.

## 3. Limites Por Modulo

- `Organization`: dono da hierarquia e dos templates de workflow
- `Process`: dono do fluxo administrativo, despacho e leitura analitica do processo
- `Task`: dono da execucao operacional
- `Assets`: dono do patrimonio operacional
- `TimeClock`: dono do registro de ponto
- `Administration`: dono de usuarios, permissoes e catalogos administrativos
- `Configuration`: dono das referencias base
- `Auth`, `Profile`, `Public`, `Dashboard`, `Audit`: borda, suporte e observabilidade

## 4. Acoplamentos Sensiveis

- `Organization -> Process` por workflow e setor atual
- `Organization -> Task` por `organization_id`
- `Task -> Administration` por usuarios e catalogos
- `Assets -> Administration` por usuarios, fornecedores e produtos
- `Assets -> Configuration` por unidade, setor e bloco financeiro
- `TimeClock -> Administration` por usuario
- `Profile/Auth -> Administration` por identidade
- `Dashboard -> Task/Process/Notification` por agregacao

## 5. Checklist De Revisao Estrutural

### Services

- [ ] Regra de negocio esta em service?
- [ ] Fluxo critico usa transacao quando necessario?
- [ ] Historico operacional ou institucional foi registrado?
- [ ] Notificacoes foram disparadas apenas no ponto certo do fluxo?

### Livewire / Controllers

- [ ] Camada apenas orquestra entrada e estado?
- [ ] Sem escrita direta de dominio sensivel?
- [ ] Autorizacao consistente entre rota, policy e contexto?

### Models

- [ ] Relacoes, `fillable` e `casts` coerentes?
- [ ] Sem logica pesada em eventos de model?

### Migrations / Seeders

- [ ] FK, unique e defaults sustentam invariantes?
- [ ] Seeder esta alinhado ao schema atual e ao RBAC do Spatie?

### Views

- [ ] Blade declarativa, sem query de dominio?
- [ ] Texto da UI em PT-BR?
- [ ] Estado Alpine extraido em telas extensas?
- [ ] Browser log sem erro antes de concluir diagnostico de backend?
- [ ] Sem mojibake (`rg -n "Ãƒ|Ã‚|ï¿½|Ã¢|Ã…" resources/views app/Livewire`)?

## 6. Criterio De Pronto

Uma mudanca estrutural so esta pronta quando:

- integridade da hierarquia esta preservada
- limites de modulo continuam claros
- regras nao estao duplicadas sem necessidade
- autorizacao esta consistente
- impacto de performance foi considerado
- risco residual esta documentado
