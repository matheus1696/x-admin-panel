# Prompt Master - Process Module (X-AdminPanel)
Laravel 12 + Livewire 3 + AlpineJS + Spatie Permission

UI: PT-BR (UTF-8) via lang files
Code: English + camelCase (classes PascalCase)
Context: Existing X-AdminPanel module (must follow docs-ai standards)

---

## 0) GLOBAL PROMPT (send once before any stage)

You are implementing a NEW module named `Process` inside an EXISTING Laravel 12 system (X-AdminPanel).

### Non-negotiables
- Follow docs-ai architecture and guardrails first.
- Keep layer flow: `Services -> Controllers/Livewire -> Blade`.
- Keep business rules in Services, not in Livewire/Blade.
- Do not create a parallel hierarchy engine.
- Do not duplicate workflow step ordering logic outside existing workflow services.
- Do not bypass `TaskService` for operational task writes.
- Use Spatie Permission + Policies for authorization.
- Keep process timeline append-only for auditability.
- UI strings must be PT-BR via `lang/pt_BR`.

### System boundaries
- `Organization` remains the single structural source (hierarchy + workflow structure).
- `Task` remains the single operational execution source.
- `Process` coordinates lifecycle, decisions, and traceability across modules.

### Delivery behavior
- Implement one stage at a time.
- Include tests in each stage where requested.
- List created/modified files after each stage.
- Do not implement future stages early.

---

## 1) MODULE GOAL

Build a Process module that can:
- open and track administrative processes
- bind a process to organizational structure
- trigger and observe execution in Task
- provide timeline visibility for decisions/events

The module should coordinate, not replace existing core modules.

---

## 2) DATA MODEL PRINCIPLES

Allowed new entities (module-owned):
- `processes`
- `process_events`
- `process_participants` (optional)
- `process_attachments` (optional)

Do not recreate:
- workflow templates/steps already owned by `Organization`
- task execution entities already owned by `Task`
- RBAC structures already owned by `Administration` + Spatie

---

## 3) STAGE PROMPTS (run sequentially)

## Stage 0 - Bootstrap, Config, Permissions, Translations
Implement:
1) Config
- `config/process.php` with:
  - `auto_start_task` (bool, default true)
  - `timeline_append_only` (bool, default true)
  - `default_priority` (string, default `normal`)

2) Enum
- `app/Enums/Process/ProcessStatus.php`
  - `OPEN`
  - `IN_PROGRESS`
  - `ON_HOLD`
  - `CLOSED`
  - `CANCELLED`

3) Seeder (Spatie)
- `database/seeders/ProcessPermissionsSeeder.php`
  - `process.view`
  - `process.create`
  - `process.manage`
  - `process.close`
  - `process.timeline.view`

4) Translations
- `lang/pt_BR/process.php` with at least:
  - `menu.*`
  - `actions.*`
  - `fields.*`
  - `statuses.*`
  - `messages.*`

Tests:
- config loads
- enum values exist
- seeder creates permissions

Constraints:
- No migrations yet
- No Livewire yet

---

## Stage 1 - Migrations & Models (Core)
Implement migrations + models for:
- `processes`
- `process_events`

### `processes` columns
- id (use project standard key type)
- code (unique)
- title
- description (nullable)
- organization_id (nullable FK to organization structure if required by flow)
- workflow_id (nullable FK to workflow template if used)
- opened_by (FK users)
- owner_id (nullable FK users)
- priority (string)
- status (string)
- started_at (nullable timestamp)
- closed_at (nullable timestamp)
- timestamps

Indexes:
- `(status, created_at)`
- `(organization_id, created_at)`
- `(opened_by, created_at)`

### `process_events` columns
- id
- process_id (FK)
- event_type (string)
- actor_id (nullable FK users)
- payload (json nullable)
- created_at (timestamp)

Indexes:
- `(process_id, created_at)`
- `(event_type, created_at)`

Models:
- `Process`
- `ProcessEvent`

Relations:
- process -> openedBy/owner/user
- process -> events

Tests:
- migrations run
- basic relation sanity tests

Constraints:
- keep `process_events` append-only

---

## Stage 2 - DTOs
Implement DTOs in `app/DTOs/Process/`:
- `OpenProcessDTO`
- `StartProcessDTO`
- `CloseProcessDTO`
- `LogProcessEventDTO`

Requirements:
- typed properties
- named arguments in constructors
- no request objects inside DTOs

---

## Stage 3 - Validation Layer
Implement validators in `app/Validations/Process/`:
- `OpenProcessValidator`
- `StatusTransitionValidator`
- `CloseProcessValidator`
- `ProcessOwnershipValidator` (context checks)

Exception:
- `ProcessValidationException` (if no system standard exists)

Tests:
- invalid open payload
- invalid status transitions
- close validation rules

---

## Stage 4 - Services (Transactional Core)
Implement services in `app/Services/Process/`:
- `ProcessService`
- `ProcessEventService`
- `ProcessOrchestrationService`

Minimum methods:
- `ProcessService::open(OpenProcessDTO $dto): Process`
- `ProcessService::start(StartProcessDTO $dto): Process`
- `ProcessService::close(CloseProcessDTO $dto): Process`
- `ProcessEventService::log(LogProcessEventDTO $dto): ProcessEvent`
- `ProcessOrchestrationService::startExecution(Process $process): void`

Rules:
- critical writes must be transactional
- status changes must validate allowed transitions
- event log is append-only
- if execution starts, call existing `TaskService` instead of direct task writes

Tests:
- open/start/close happy paths
- invalid transition rejection
- event logging assertions
- orchestration uses service boundary

---

## Stage 5 - Policies
Implement:
- `ProcessPolicy`
- `ProcessEventPolicy` (if needed)

Policy abilities:
- `view`
- `viewAny`
- `create`
- `manage`
- `close`
- `viewTimeline`

Rules:
- owner/requester can view own process
- elevated users can manage/close based on permission
- timeline visibility follows `process.timeline.view` or contextual access

Tests:
- allow/deny matrix by permission + ownership

---

## Stage 6 - HTTP + Livewire (Core UI)
Implement:
- process listing page
- process detail page (with timeline)
- process open form
- process close action

Conventions:
- reuse existing layout and UI components
- no hardcoded PT strings
- no business logic in components

Authorization:
- middleware + policy checks must be coherent

---

## Stage 7 - Timeline & Attachments (Optional in MVP+)
Implement:
- timeline feed component
- attachment upload/list (if approved)

Rules:
- every sensitive action adds a process event
- attachment operations should log events
- use system storage conventions

Tests:
- timeline ordering
- attachment event logging

---

## Stage 8 - Task Integration
Implement integration flow:
- from process start -> request operational execution via `TaskService`
- store reference links in process context (`task_id`, hub/context IDs when needed)
- keep ownership/access rules from Task unchanged

Tests:
- integration happy path
- no direct task write bypass

---

## Stage 9 - Reporting & Query Safety
Implement:
- filterable list (status, period, owner, organization)
- summary counters (open/in progress/closed)

Rules:
- avoid N+1
- add indexes only for validated query paths

Tests:
- filter behavior
- permission-scoped visibility

---

## Stage 10 - Hardening
Implement:
- additional audit context in event payloads
- performance pass on main list/detail queries
- concurrency checks on close/start operations if race risk exists

Output:
- list of hardened points
- residual risks

---

## 4) FINAL QUALITY CHECKLIST

Before finishing any Process stage, verify:
1. No hierarchy logic was duplicated outside `Organization`.
2. No operational task rule bypassed `TaskService`.
3. Service boundaries remain explicit and coherent.
4. Timeline/events remain append-only.
5. Authorization is consistent across route, policy, and context.
6. Query performance and N+1 risk were considered.
