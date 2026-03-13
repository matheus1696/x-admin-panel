# TimeClock Guide

Guia de manutencao do modulo `TimeClock` no estado atual do projeto.
Atualizado em: 2026-03-12.

## Escopo Atual

O modulo cobre:

- registro de ponto com foto e geolocalizacao
- listagem de registros proprios
- listagem administrativa de registros
- cadastro de locais permitidos
- relatorios e exportacao CSV

## Arquitetura Atual

Services:

- `TimeClockEntryService`
- `TimeClockLocationService`
- `TimeClockReportService`

Validadores:

- `PhotoRequiredValidator`
- `GpsRequiredValidator`
- `RegisterRateLimitValidator`
- `LocationWithinRadiusValidator`

Policies:

- `TimeClockEntryPolicy`
- `TimeClockLocationPolicy`

## Regras Que Nao Podem Ser Quebradas

- validacao de raio do local fica no service
- status do registro reflete resultado das validacoes
- relatorio e exportacao nao alteram dominio
- gestao de locais e listagens administrativas dependem de policy

## Interfaces Principais

- `RegisterEntry`
- `MyEntries`
- `EntriesIndex`
- `EntryShow`
- `LocationsIndex`
- `ReportsIndex`

## Checklist Antes De Alterar

1. O registro ainda passa por DTO + service + validadores?
2. Rate limit continua protegido fora da UI?
3. Policies continuam cobrindo registro, leitura e gestao?
4. Dados sensiveis do ponto continuam expostos apenas para quem pode ver?
5. Exportacao CSV continua derivada do relatorio oficial?

## Expansao Segura

- adicionar nova regra operacional em validador dedicado
- manter `TimeClockEntryService` como porta unica de registro
- evitar logica de GPS, foto ou raio dentro do Livewire
