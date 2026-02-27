# SYSTEM_MAP - X-AdminPanel

Senior engineer onboarding map for the current codebase. This is a structural guide: what lives where, how features flow, and the domain boundaries we protect.

## Product Intent (Non-Negotiable)
- X-AdminPanel is an organizational visualization platform, not a generic CRUD admin.
- Primary value is hierarchy clarity and strategic structural understanding.
- Few Clicks Principle: minimize navigation depth and steps without reducing clarity.

## High-Level Architecture
- Laravel 12 app with Livewire v3 + Blade UI.
- Thin controllers, business logic in Services, UI state in Livewire.
- Spatie Permissions for roles/abilities.
- Database driver is configurable; README expects Postgres. A local SQLite file exists for dev.

## Core Stack Guidelines
- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Blade + Livewire v3
- Interatividade: Alpine.js
- Permissoes: Spatie Laravel Permission
- Banco de dados: configuravel (README assume Postgres; SQLite local para dev)

## Module Map (Routes -> Livewire/Controllers)
Public
- `/` redirects to login.
- `/contatos` -> `App\Livewire\Public\Contact\ContactPage`.

Authenticated (`auth`, `verified`)
- Dashboard: `/dashboard` -> `App\Http\Controllers\DashboardController@index`.
- Organogram:
  - `/organograma` -> `App\Livewire\Organization\OrganizationChart\OrganizationChartDashboardPage`
  - `/organograma/full` -> `App\Livewire\Organization\OrganizationChart\OrganizationChartDashboardFullPage`
- Tasks:
  - `/tarefas` -> `App\Livewire\Task\TaskHubPage`
  - `/tarefas/{uuid}` -> `App\Livewire\Task\TaskPage`
- Profile:
  - `/perfil/editar` -> `ProfileController@edit`
  - `/perfil/update` -> `ProfileController@update`
  - `/perfil/senha` -> `ProfileController@password` + `passwordUpdate`
- Administration:
  - `/administracao/usuarios` -> `App\Livewire\Administration\User\UserPage`
  - `/administracao/tasks/status` -> `App\Livewire\Administration\Task\TaskStatusPage`
  - `/administracao/tasks/categorias` -> `App\Livewire\Administration\Task\TaskStatusPage` (same page)
- System Configuration:
  - Establishments:
    - `/configuracao/estabelecimentos/lista` -> `EstablishmentList`
    - `/configuracao/estabelecimentos/unidade/{code}` -> `EstablishmentShow`
    - `/configuracao/estabelecimentos/tipos` -> `EstablishmentTypePage`
  - Occupations: `/configuracao/ocupacoes` -> `OccupationPage`
  - Financial Blocks: `/configuracao/financeiro/blocos` -> `FinancialBlockPage`
  - Regions:
    - `/configuracao/regioes/paises` -> `RegionCountryPage`
    - `/configuracao/regioes/estados` -> `RegionStatePage`
    - `/configuracao/regioes/cidades` -> `RegionCityPage`
- Organization:
  - `/organizacao/configuracao` -> `OrganizationChartConfigPage`
  - `/organizacao/workflow` -> `WorkflowProcessesPage`
- Audit:
  - `/auditoria/logs` -> `App\Http\Controllers\Audit\LogController@index`

Route source of truth: `routes/web.php`.

## Core Domain: Organogram (Hierarchy Integrity)
Primary model: `App\Models\Organization\OrganizationChart\OrganizationChart`
- Self-referential tree via `hierarchy` (parent id).
- `children()` relationship filters `is_active` and orders by `order`.

Primary service: `App\Services\Organization\OrganizationChart\OrganizationChartService`
- `tree()` loads hierarchy with eager loading.
- `reorder()` recalculates `order` and `number_hierarchy` to preserve structural ordering.

Key UI:
- `resources/views/livewire/organization/organization-chart/*`
- Partial for node rendering: `resources/views/livewire/organization/organization-chart/_partials/organization-chart-org-node.blade.php`

## Workflow (Organization Processes)
Models:
- `Workflow` and `WorkflowStep` in `app/Models/Organization/Workflow/`.
- Steps can link to `OrganizationChart` via `organization_id`.

Services:
- `WorkflowService` (CRUD + status toggle).
- `WorkflowStepService` (ordering, deadline aggregation into workflow total).

UI:
- `App\Livewire\Organization\Workflow\WorkflowProcessesPage`
- `App\Livewire\Organization\Workflow\WorkflowSteps`
- Views in `resources/views/livewire/organization/workflow/*`

## Tasks (Operational Execution)
Models:
- `TaskHub` = container for tasks and steps.
- `TaskHubMember` = user membership/share for a hub.
- `Task` = main task entity.
- `TaskStep` = sub-step, can be linked to `OrganizationChart`.
- Activity tables: `TaskActivity`, `TaskStepActivity`.

Service:
- `App\Services\Task\TaskService` for task listing, create, update, comments, kanban moves.
- Codes are auto-generated on create based on hub acronym.

UI:
- `App\Livewire\Task\TaskHubPage` (hub list and creation)
- `App\Livewire\Task\TaskPage` (tasks, kanban, steps, members)
- `resources/views/livewire/task/*`

Membership behavior:
- Hub membership is managed in TaskPage (Membros tab).
- TaskHubPage lists hubs owned by or shared with the user.

## Administration & Configuration
Administration
- Users in `app/Models/Administration/User/User.php` with Spatie roles/permissions.
- Task taxonomy in `app/Models/Administration/Task/*`.
- User management in `App\Livewire\Administration\User\UserPage`.

Configuration
- Establishments, departments, establishment types in `app/Models/Configuration/Establishment/*`.
- Financial blocks, regions, occupations under `app/Models/Configuration/*`.
- Livewire pages in `app/Livewire/Configuration/*`.

## Validation & Requests
- Form Requests: `app/Http/Requests/**` for profile, workflow, organization chart, user admin.
- Rule containers: `app/Validation/**` (domain-specific rules, reused by Livewire/services).

## UI Layer
- Livewire components under `app/Livewire/**`.
- Blade components under `resources/views/components/**`.
- Layouts: `resources/views/layouts/app.blade.php` and `resources/views/layouts/guest.blade.php`.
- Sidebar components in `resources/views/components/sidebar/*`.

## Cross-Cutting Concerns
Auth & Permissions
- Breeze auth scaffolding + MustVerifyEmail.
- Spatie permissions; route middleware uses `can:*` abilities.

Exceptions
- Custom 403 handling in `bootstrap/app.php` redirects back to dashboard with a flash message.

Traits
- `HasUuid`, `HasUuidRouteKey` for UUIDs and route binding.
- `HasActive`, `HasTitleFilter` for consistent status and filtering behavior.

## Database Footprint (Migrations)
Key migration groupings:
- Core auth + permissions + activity logs.
- Organization chart + workflows + workflow steps.
- Task hubs, tasks, task steps, statuses, categories, priorities.
- Establishments, departments, occupations, regions, financial blocks.

Migrations live in `database/migrations`.

## Dev & Test Workflow
Common scripts (Composer):
- `composer run setup`
- `composer run dev`
- `composer run test`

Tests:
- Pest-based; tests in `tests/Feature` and `tests/Unit`.

## Architecture Guardrails (Do Not Break)
- Organogram integrity is sacred: hierarchy clarity > features.
- Prefer inline/contextual edits and fewer steps.
- No business logic in controllers or views.
- Avoid duplicated hierarchy logic; services should be the single source of truth.

## Where to Start for Changes
- UI behavior: start in Livewire component class and its Blade view.
- Domain rules: check `app/Services` and `app/Validation`.
- Data structure: check model + migration.
- Permission changes: update policy/permission usage and route middleware.
