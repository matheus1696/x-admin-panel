# System Map

## 1. Precedencia Documental

1. `AGENTS.md`
2. `SYSTEM_GUIDE.md`
3. `ARCHITECTURE.md`
4. `DOMAINS.md`
5. `domains/*.md`
6. `CONVENTIONS.md`
7. `README.md`

Em conflito, o item de maior precedencia vence.

## 2. Papel De Cada Documento

- `SYSTEM_GUIDE.md`: invariantes, limites por modulo e checklist de revisao
- `ARCHITECTURE.md`: relacao entre modulos e camadas
- `DOMAINS.md`: selecao rapida do dominio
- `domains/*.md`: regras locais de cada modulo
- `CONVENTIONS.md`: padrao de implementacao
- `README.md`: indice de navegacao

## 3. Dependencia Entre Modulos

Cadeia principal observada:

`Organization -> Task -> Administration`

Dependencias de suporte:

- `Administration -> Configuration`
- `Auth/Profile -> Administration`
- `Profile/Public -> Configuration`
- `Dashboard/Profile -> Audit`

## 4. Fluxo De Leitura Por Tipo De Mudanca

Mudanca estrutural:
1. `AGENTS.md`
2. `SYSTEM_GUIDE.md`
3. `ARCHITECTURE.md`
4. dominio principal
5. dominios dependentes

Mudanca de execucao em Task:
1. `SYSTEM_GUIDE.md`
2. `domains/TASK.md`
3. `domains/ORGANIZATION.md`
4. `domains/ADMINISTRATION.md`

Mudanca de acesso e identidade:
1. `domains/AUTH.md`
2. `domains/PROFILE.md`
3. `domains/ADMINISTRATION.md`

## 5. Zonas De Atencao

- Integridade da hierarquia em `OrganizationChart`
- Acoplamento remanescente em `TaskPage`
- Regras terminais baseadas em status
- Duplicacao de autorizacao entre rota e componente
- Coerencia de catalogos por hub (`task_hub_id`)
