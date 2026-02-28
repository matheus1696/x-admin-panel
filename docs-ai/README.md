# AI Docs Index

Entrada principal para agentes.

Leia nesta ordem:
1. `docs-ai/DOMAINS.md`
2. `docs-ai/ARCHITECTURE.md`
3. `docs-ai/CONVENTIONS.md`
4. `docs-ai/domains/<MODULO>.md`

## Quando Ler Cada Arquivo

`docs-ai/DOMAINS.md`
- Para localizar o domínio correto.
- Use antes de abrir um arquivo detalhado.

`docs-ai/ARCHITECTURE.md`
- Para entender fluxo entre módulos e dependências principais.
- Use antes de refatorações ou mudanças que cruzam domínios.

`docs-ai/CONVENTIONS.md`
- Para seguir o padrão real do repositório.
- Use antes de criar controller, service, Livewire ou teste.

`docs-ai/domains/`
- Para detalhes por módulo.
- Cada arquivo cobre responsabilidade, entidades, fluxos, regras, integrações e riscos.

## Atalhos

- Hierarquia ou workflow: `ARCHITECTURE.md`, `domains/ORGANIZATION.md`
- Kanban, tasks ou steps: `domains/TASK.md`, `CONVENTIONS.md`
- Usuários, permissões ou catálogos: `domains/ADMINISTRATION.md`
- Cadastros base ou contatos: `domains/CONFIGURATION.md`, `domains/PUBLIC.md`
- Login, senha ou perfil: `domains/AUTH.md`, `domains/PROFILE.md`
- Logs: `domains/AUDIT.md`

## Regras De Uso

- Não use este índice como fonte de regra de negócio.
- Não replique conteúdo do índice em outros arquivos.
- Se a mudança for estrutural, leia também o arquivo do domínio afetado.
