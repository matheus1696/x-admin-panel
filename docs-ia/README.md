# Docs IA Index

Entrada principal da documentacao de manutencao do X-AdminPanel.
Atualizado em: 2026-03-12.

## Objetivo

Esta pasta existe para reduzir ambiguidade tecnica.
Ela descreve:

- dominios reais do sistema
- fronteiras entre modulos
- invariantes que nao podem ser quebrados
- estado atual de implementacao
- pontos de expansao seguros

## Ordem De Leitura

1. `DOMAINS.md`
2. `ARCHITECTURE.md`
3. `SYSTEM_GUIDE.md`
4. `CONVENTIONS.md`
5. `COMPONENTS.md`
6. `LAYOUT_GUIDE.md`
7. `domains/<MODULO>.md`
8. `module/<MODULO>.md`
9. `SYSTEM_MAP.md`

## Quando Ler Cada Arquivo

`DOMAINS.md`
- para identificar rapidamente onde a mudanca realmente pertence

`ARCHITECTURE.md`
- para entender dependencias entre modulos, camadas e responsabilidades

`SYSTEM_GUIDE.md`
- para validar invariantes, fluxos criticos e checklist de revisao

`CONVENTIONS.md`
- para seguir o padrao real de implementacao do projeto

`COMPONENTS.md`
- para consultar a API atual dos componentes Blade compartilhados

`LAYOUT_GUIDE.md`
- para mudancas de composicao visual, consistencia de UI e reutilizacao de componentes

`domains/*.md`
- para regras, riscos e integracoes do dominio afetado

`module/*.md`
- para manutencao orientada ao estado atual dos modulos mais sensiveis

`SYSTEM_MAP.md`
- para resolver conflito documental e decidir precedencia

## Atalhos

- Hierarquia e workflow: `domains/ORGANIZATION.md`
- Processos: `domains/PROCESS.md`
- Tarefas e kanban: `domains/TASK.md`
- Patrimonio e auditoria de ativos: `domains/ASSETS.md`
- Controle de ponto: `domains/TIME_CLOCK.md`
- Componentes Blade compartilhados: `COMPONENTS.md`
- Layout e padronizacao visual: `LAYOUT_GUIDE.md`
- Usuarios e permissoes: `domains/ADMINISTRATION.md`
- Cadastros base: `domains/CONFIGURATION.md`
- Acesso, perfil e dashboard: `domains/AUTH.md`, `domains/PROFILE.md`, `domains/DASHBOARD.md`
- Rastreabilidade geral: `domains/AUDIT.md`

## Regras De Uso

- Esta pasta nao substitui leitura do codigo quando a mudanca for sensivel.
- Em mudanca estrutural, leia o dominio principal e os dominios dependentes.
- Em conflito entre documentos, siga `SYSTEM_MAP.md`.
