# System Map

Atualizado em: 2026-03-12.

## 1. Precedencia Documental

1. `AGENTS.md`
2. `SYSTEM_GUIDE.md`
3. `ARCHITECTURE.md`
4. `DOMAINS.md`
5. `domains/*.md`
6. `module/*.md`
7. `CONVENTIONS.md`
8. `COMPONENTS.md`
9. `LAYOUT_GUIDE.md`
10. `README.md`

Em conflito, o item de maior precedencia vence.

## 2. Papel De Cada Documento

- `SYSTEM_GUIDE.md`: invariantes, fluxos criticos e checklist de revisao
- `ARCHITECTURE.md`: relacao entre modulos e camadas
- `DOMAINS.md`: selecao rapida do dominio
- `domains/*.md`: regras locais, rotas, services e riscos do modulo
- `module/*.md`: guias de manutencao dos modulos mais sensiveis
- `CONVENTIONS.md`: padrao de implementacao
- `COMPONENTS.md`: API atual dos componentes Blade compartilhados
- `LAYOUT_GUIDE.md`: playbook de consistencia visual e composicao
- `README.md`: indice de navegacao

## 3. Dependencias Entre Modulos

Cadeia principal observada:

`Organization -> Process`
`Organization -> Task`

Dependencias de suporte:

- `Task -> Administration`
- `Assets -> Administration`
- `Assets -> Configuration`
- `TimeClock -> Administration`
- `Administration -> Configuration`
- `Auth/Profile -> Administration`
- `Public -> Configuration`
- `Dashboard -> Task/Process/Notification`

## 4. Fluxo De Leitura Por Tipo De Mudanca

Mudanca estrutural:
1. `AGENTS.md`
2. `SYSTEM_GUIDE.md`
3. `ARCHITECTURE.md`
4. dominio principal
5. dominios dependentes

Mudanca em processo:
1. `SYSTEM_GUIDE.md`
2. `domains/PROCESS.md`
3. `domains/ORGANIZATION.md`
4. `domains/ADMINISTRATION.md`

Mudanca em task:
1. `SYSTEM_GUIDE.md`
2. `domains/TASK.md`
3. `domains/ORGANIZATION.md`
4. `domains/ADMINISTRATION.md`

Mudanca de patrimonio:
1. `SYSTEM_GUIDE.md`
2. `domains/ASSETS.md`
3. `domains/ADMINISTRATION.md`
4. `domains/CONFIGURATION.md`

Mudanca em controle de ponto:
1. `SYSTEM_GUIDE.md`
2. `domains/TIME_CLOCK.md`
3. `domains/ADMINISTRATION.md`

Mudanca de acesso e identidade:
1. `domains/AUTH.md`
2. `domains/PROFILE.md`
3. `domains/ADMINISTRATION.md`

## 5. Zonas De Atencao

- integridade da hierarquia em `OrganizationChart`
- etapa corrente unica em `Process`
- acoplamento remanescente em `TaskPage`
- separacao estoque x lista operacional em `Assets`
- coerencia de locais e raio em `TimeClock`
- duplicacao de autorizacao entre rota, policy e componente
- coerencia de catalogos por hub em `Task`
