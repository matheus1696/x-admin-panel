# Public

## Responsabilidade

`Public` é a superfície aberta atual. No código existente, ela se resume a `ContactPage`, que expõe contatos por estabelecimento e departamento.

## Entidades Principais

- `ContactPage`
- `Department`
- `Establishment`

## Fluxos Críticos

- Buscar contatos por estabelecimento, departamento ou texto
- Abrir modal com departamentos de uma unidade
- Recarregar departamentos ao mudar o filtro local

## Invariantes / Regras

- o módulo é somente leitura
- o detalhe depende de um `Establishment` existente
- a lista detalhada sempre usa `selectedEstablishmentId`
- a busca principal cruza `Department` e `Establishment`

## Integrações

- `Configuration`: fornece `Department` e `Establishment`
- `Livewire`: usa a trait `Modal`

## Riscos / Armadilhas

- adicionar mutação de dados nesse módulo
- duplicar regras de filtro já tratadas no modelo/consulta
- assumir que toda unidade terá departamentos
