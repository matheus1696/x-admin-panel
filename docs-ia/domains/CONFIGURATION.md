# Configuration

## Responsabilidade

`Configuration` mantem cadastros base compartilhados pelos outros modulos.
Hoje ele concentra:

- estabelecimentos
- departamentos
- tipos de estabelecimento
- paises, estados e cidades
- ocupacoes
- blocos financeiros

## Entidades E Services Principais

- `Establishment`
- `Department`
- `EstablishmentType`
- `RegionCountry`
- `RegionState`
- `RegionCity`
- `Occupation`
- `FinancialBlock`
- `EstablishmentService`
- `DepartmentService`

## Interfaces Principais

- `configuration.manage.establishments.view`
- `configuration.manage.establishments.show`
- `configuration.manage.establishments.types`
- `configuration.manage.occupations`
- `configuration.manage.financial.blocks`
- `configuration.manage.regions.countries|states|cities`

## Fluxos Criticos

- listar, criar e atualizar estabelecimentos
- exibir unidade por `code`
- criar e atualizar departamentos
- manter tipos de estabelecimento
- manter paises, estados e cidades
- fornecer dados para contato publico e patrimonio

## Invariantes

- `Department` pertence a `Establishment`
- `RegionCity` pertence a `RegionState`
- `RegionState` pertence a `RegionCountry`
- `Establishment` se relaciona com cidade, tipo e bloco financeiro
- buscas usam campos como `filter`
- `EstablishmentService::show()` usa `code`
- `Occupation` e usada por `User`

## Integracoes

- `Public`: consulta `Department` e `Establishment`
- `Administration`: `User` referencia `Occupation`
- `Assets`: usa setor, unidade e bloco financeiro
- `Profile`: carrega `Occupation`

## Riscos

- quebrar a cadeia pais -> estado -> cidade afeta cadastros dependentes
- tratar `Department` como entidade solta enfraquece o vinculo com a unidade
- trocar a chave `code` sem alinhar a rota quebra lookup de unidade
