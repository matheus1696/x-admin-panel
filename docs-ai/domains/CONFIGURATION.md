# Configuration

## Responsabilidade

`Configuration` mantém cadastros base usados por outros módulos. Hoje ele concentra estabelecimentos, departamentos, regiões, ocupações, tipos de estabelecimento e blocos financeiros.

## Entidades Principais

- `Establishment`
- `Department`
- `EstablishmentType`
- `RegionCountry`
- `RegionState`
- `RegionCity`
- `Occupation`
- `FinancialBlock`
- `EstablishmentService`, `DepartmentService`

## Fluxos Críticos

- Listar, criar e atualizar estabelecimentos
- Exibir unidade por `code`
- Criar e atualizar departamentos
- Manter tipos de estabelecimento
- Manter países, estados e cidades
- Expor dados para a página pública de contatos

## Invariantes / Regras

- `Department` pertence a `Establishment`
- `RegionCity` pertence a `RegionState`
- `RegionState` pertence a `RegionCountry`
- `Establishment` se relaciona com cidade, tipo e bloco financeiro
- buscas usam campos como `filter`
- `EstablishmentService::show()` usa `code`
- `Occupation` é usada por `User`

## Integrações

- `Public`: consulta `Department` e `Establishment`
- `Administration`: `User` referencia `Occupation`
- `Profile`: carrega `Occupation`

## Riscos / Armadilhas

- quebrar a cadeia país -> estado -> cidade afeta cadastros dependentes
- tratar `Department` como entidade solta enfraquece o vínculo com a unidade
- trocar a chave `code` sem alinhar a rota quebra lookup de unidade
