# X-AdminPanel - Organizational Intelligence Platform

O **X-AdminPanel** e uma plataforma de visualizacao organizacional focada em clareza hierarquica, entendimento estrutural e governanca administrativa.

Nao e um CRUD generico. O valor principal esta em:
- clareza de hierarquia
- visualizacao consistente das relacoes
- poucas etapas para executar tarefas relevantes

---

## Modulos Implementados

### Organizacao (Organograma)
- Visualizacao do organograma corporativo
- Gestao administrativa da hierarquia
- Estruturacao por setores e niveis hierarquicos

### Workflow
- Definicao de processos organizacionais
- Etapas com ordenacao e prazos
- Integracao direta com tarefas

### Tarefas (Tasks)
- Ambientes (TaskHub) como container de tarefas e etapas
- Kanban e dashboard operacional
- Status, categorias e prioridades administrativas
- Rastreabilidade por atividades

### Administracao
- Gestao de usuarios
- Permissoes e acessos (Spatie)

### Configuracoes do Sistema
- Estabelecimentos e departamentos
- Ocupacoes (CBO)
- Regioes (pais, estado, cidade)
- Blocos financeiros

### Auditoria
- Logs administrativos
- Rastreabilidade de acoes

---

## Tecnologias

- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Blade + Livewire v3
- Interatividade: Alpine.js
- Permissoes: Spatie Laravel Permission
- Banco de dados: configuravel (README assume Postgres; SQLite local para dev)

---

## Instalacao

Pre-requisitos: PHP 8.2+, Composer, Node.js, banco de dados configurado.

1. Instale dependencias e prepare o ambiente:

```bash
composer run setup
```

2. Inicie o ambiente de desenvolvimento:

```bash
composer run dev
```

3. Testes:

```bash
composer run test
```

---

## Estrutura e Fluxo

- Rotas: `routes/web.php`
- UI: `app/Livewire/**` e `resources/views/livewire/**`
- Regras: `app/Services/**` e `app/Validation/**`
- Modelos: `app/Models/**`

Regras de arquitetura e visao do sistema:
- `AGENTS.md`
- `docs/SYSTEM_MAP.md`

---

## Autoria

Projeto desenvolvido por Webxperts
Matheus Andre Bezerra Mota
