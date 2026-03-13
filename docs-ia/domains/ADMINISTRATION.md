# Administration

## Responsabilidade

`Administration` governa a identidade institucional e os catalogos administrativos do sistema.
Hoje o modulo cobre:

- usuarios
- permissoes e roles via Spatie Permission
- fornecedores
- produtos, tipos de produto e unidades de medida
- catalogos usados por `Task`

## Entidades E Services Principais

- `User`
- `Gender`
- `Supplier`
- `Product`
- `ProductType`
- `ProductMeasureUnit`
- `TaskStatus`
- `TaskStepStatus`
- `TaskCategory`
- `TaskPriority`
- `TaskStepCategory`
- `UserService`
- `TaskStatusService`
- `TaskStepStatusService`
- `SupplierService`
- `ProductService`
- `ProductTypeService`
- `ProductMeasureUnitService`

## Interfaces Principais

- `administration.manage.users`
- `administration.manage.users.permissions`
- `administration.manage.suppliers`
- `administration.manage.products`
- `administration.manage.product-types`
- `administration.manage.product-measure-units`
- `administration.manage.tasks.status`
- `administration.manage.tasks.category`

## Fluxos Criticos

- criar e atualizar usuarios
- ativar e desativar usuarios
- sincronizar roles e permissoes e encerrar sessoes abertas
- manter fornecedores e produtos
- manter catalogos ativos de `Task`
- expor catalogos operacionais para `TaskPage`

## Invariantes

- `User` e a identidade autenticavel principal
- permissoes sao sincronizadas via Spatie Permission
- alteracao de permissao remove sessoes existentes do usuario
- status usados em execucao devem respeitar `is_active = true`
- catalogos de task precisam continuar coerentes por hub
- `name_filter` e derivado do nome para pesquisa

## Integracoes

- `Task`: fornece status, categorias, prioridades e responsaveis
- `Assets`: fornece fornecedores, produtos e unidades de medida
- `Auth`: usa `User`
- `Configuration`: `User` referencia `Occupation`
- `Profile`: reutiliza `User`, `Gender` e `Occupation`

## Riscos

- mudar nomes de status sem revisar `TaskService` afeta logica por titulo
- misturar catalogo administrativo com regra de execucao confunde contexto
- aplicar permissao so na UI deixa a borda inconsistente
- sincronizar permissao sem limpar sessao deixa acesso residual
