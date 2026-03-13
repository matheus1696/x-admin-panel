# TimeClock

## Responsabilidade

`TimeClock` governa o registro de ponto, os locais permitidos para batida e os relatorios administrativos do modulo.

## Entidades E Services Principais

- `TimeClockEntry`
- `TimeClockLocation`
- `TimeClockEntryService`
- `TimeClockLocationService`
- `TimeClockReportService`
- `RegisterTimeClockEntryDTO`

## Validadores E Policies Relevantes

- `PhotoRequiredValidator`
- `GpsRequiredValidator`
- `RegisterRateLimitValidator`
- `LocationWithinRadiusValidator`
- `TimeClockEntryPolicy`
- `TimeClockLocationPolicy`

## Interfaces Principais

- `time-clock.register`
- `time-clock.my-entries`
- `time-clock.entries.index`
- `time-clock.entries.show`
- `time-clock.locations.index`
- `time-clock.reports.index`

## Fluxos Criticos

- registrar ponto com foto, GPS, precisao e local
- listar registros proprios
- listar registros gerais por permissao
- manter locais de registro
- exportar relatorio CSV
- listar usuarios ativos sem registro no dia

## Invariantes

- o registro sempre passa pelos validadores de dominio
- validacao de raio do local nao pode ser movida para a UI
- `TimeClockEntry` e append-only
- filtros de relatorio nao alteram o dominio
- autorizacao contextual usa policies para registro, visualizacao e gestao

## Integracoes

- `Administration`: usuario dono do registro
- `Dashboard`: pode futuramente consumir metricas, sem virar dono do dominio
- `Audit`: somente para trilha institucional complementar

## Riscos

- aceitar batida fora do raio compromete confiabilidade
- mover regra de status para Livewire gera divergencia entre telas
- usar listagem administrativa sem policy amplia exposicao de dados pessoais
