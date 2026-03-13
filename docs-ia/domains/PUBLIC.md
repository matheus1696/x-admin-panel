# Public

## Responsabilidade

`Public` e a superficie aberta atual.
No codigo existente, ela se resume a `ContactPage`, que expoe contatos por estabelecimento e departamento.

## Entidades E Componentes Principais

- `ContactPage`
- `Department`
- `Establishment`

## Fluxos Criticos

- buscar contatos por estabelecimento, departamento ou texto
- abrir modal com departamentos de uma unidade
- recarregar departamentos ao mudar o filtro local

## Invariantes

- o modulo e somente leitura
- o detalhe depende de `Establishment` existente
- a lista detalhada usa `selectedEstablishmentId`
- a busca principal cruza `Department` e `Establishment`

## Integracoes

- `Configuration`: fornece `Department` e `Establishment`
- `Livewire`: usa a trait `Modal`

## Riscos

- adicionar mutacao de dados nesse modulo
- duplicar regras de filtro ja tratadas na consulta
- assumir que toda unidade tera departamentos
