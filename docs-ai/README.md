# AI Docs Index

Entrada principal de documentacao para manutencao do X-AdminPanel.
Atualizado em: 2026-03-08.

## Idioma E Codificacao

- UI para usuario final: Portugues (PT-BR).
- Arquivos e documentacao: UTF-8.
- Codigo backend (classes, metodos e regras): Ingles.

## Ordem De Leitura

1. `docs-ai/DOMAINS.md`
2. `docs-ai/ARCHITECTURE.md`
3. `docs-ai/SYSTEM_GUIDE.md`
4. `docs-ai/CONVENTIONS.md`
5. `docs-ai/domains/<MODULO>.md`
6. `docs-ai/SYSTEM_MAP.md`

## Quando Ler Cada Arquivo

`docs-ai/DOMAINS.md`
- Escolher rapidamente o dominio correto da mudanca.

`docs-ai/ARCHITECTURE.md`
- Entender dependencias entre modulos e fluxo entre camadas.

`docs-ai/SYSTEM_GUIDE.md`
- Validar invariantes, limites de modulo e checklist de revisao.

`docs-ai/CONVENTIONS.md`
- Aplicar padrao real de implementacao (Controllers, Services, Livewire, testes).

`docs-ai/domains/*.md`
- Ler regras e riscos locais do dominio afetado.

`docs-ai/SYSTEM_MAP.md`
- Resolver conflito entre documentos e orientar leitura por tipo de mudanca.

## Atalhos

- Hierarquia e workflow: `ARCHITECTURE.md`, `SYSTEM_GUIDE.md`, `domains/ORGANIZATION.md`
- Execucao de tarefas e kanban: `SYSTEM_GUIDE.md`, `domains/TASK.md`, `CONVENTIONS.md`
- Usuarios e permissoes: `domains/ADMINISTRATION.md`, `domains/AUTH.md`, `domains/PROFILE.md`
- Cadastros base e contatos: `domains/CONFIGURATION.md`, `domains/PUBLIC.md`
- Rastreabilidade: `domains/AUDIT.md`
- Guias de novos modulos: `module/assets.md`, `module/time_clock.md`

## Regras De Uso

- Este indice nao substitui regras de dominio.
- Em mudanca estrutural, sempre leia o dominio principal e os dominios dependentes.
- Em conflito documental, seguir precedencia descrita em `SYSTEM_MAP.md`.