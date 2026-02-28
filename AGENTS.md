# X-AdminPanel - AI Working Contract

## 1. System Identity

X-AdminPanel is an Organizational Intelligence Platform.

It exists to transform hierarchy data into clear structural understanding.

This is not:
- a generic CRUD admin
- a feature accumulator
- a UI-led dashboard

This is:
- a hierarchy visualization engine
- a structural governance tool
- an administrative intelligence layer

## 2. Core Principles

Priority order:

1. Hierarchy Integrity
2. Architectural Integrity
3. Visual Clarity
4. Performance
5. Feature Expansion

Non-negotiable:

- if hierarchy integrity is at risk, reject the solution
- if business rules spread across layers, consolidate them
- if a change increases coupling without clear gain, reconsider it

Language and stack:

- UI uses PT-BR
- files and docs use UTF-8
- backend code stays in English
- Laravel 12 + PHP 8.2+
- Blade + Livewire v3 + AlpineJS

## 3. Layer Model

Expected direction:

Services
^
Controllers
^
Livewire
^
Blade

Responsibilities:

- Services own business logic and consistency rules
- Controllers handle thin HTTP flows
- Livewire manages UI state and interaction flow
- Blade stays declarative

## 4. Guardrails

Never:

- break Organogram hierarchy integrity
- duplicate hierarchy traversal logic
- move domain rules into UI
- bypass Services for business logic
- create parallel service structures for the same concern
- put query logic in Blade views
- duplicate permission checks without need
- add UI complexity without clear value

If a request conflicts with these guardrails:

- explain the risk
- propose the safer path
- refuse unsafe implementation if needed

## 5. Module Guardrails

### Organogram

Strategic core of the system.

Must:

- preserve parent-child integrity
- keep hierarchy logic centralized
- remain the single structural source used by dependent modules

Must not:

- use alternate hierarchy engines
- define structural rules on the client

### Workflow

Process model linked to `OrganizationChart`.

Must:

- use `WorkflowService` and `WorkflowStepService`
- preserve step order
- preserve deadline consistency

Must not:

- push workflow rules into UI state

### Tasks

Operational execution layer.

Must:

- use `TaskService` for task creation
- use `TaskService` for kanban moves
- use `TaskService` for task activity logging
- preserve hub ownership and member access rules

Current code note:

- `TaskPage` still holds part of the operational flow; treat this as existing reality, not as the target pattern

### Dashboard

Entry surface only.

Must not:

- become a business-logic hub
- absorb heavy structural or operational logic

## 6. Decision Rules

When changing the system:

1. protect hierarchy first
2. preserve service boundaries
3. keep modules cohesive
4. reduce duplication
5. only then optimize or expand

Avoid:

- controller-heavy rules
- service fragmentation
- tight module coupling
- premature abstraction
- overengineering

## 7. Code Review Focus

Always check for:

- business logic outside Services
- duplicated domain logic
- unsafe hierarchy changes
- permission misuse
- N+1 queries
- responsibility leakage
- coupling increase

Classify findings as:

- `Critical`
- `Structural`
- `Improvement`

## 8. Red Flags

Pause if you find:

- business logic inside Livewire
- duplicated permission evaluation
- UI deciding domain rules
- structural coupling increasing during feature work
- tree traversal performance regressions

## 9. Documentation Map

Use the docs before changing code.

Read in this order:

1. `docs-ai/README.md`
2. `docs-ai/DOMAINS.md`
3. `docs-ai/ARCHITECTURE.md`
4. `docs-ai/CONVENTIONS.md`
5. `docs-ai/domains/<MODULO>.md`
6. `docs-ai/SYSTEM_MAP.md` for cross-document navigation

What each file is for:

- `docs-ai/README.md`: entry index
- `docs-ai/DOMAINS.md`: quick module selection
- `docs-ai/ARCHITECTURE.md`: system-wide relationships and flow
- `docs-ai/CONVENTIONS.md`: how this codebase writes code
- `docs-ai/SYSTEM_MAP.md`: document precedence, reading flow, module dependency, risk map
- `docs-ai/domains/ORGANIZATION.md`: hierarchy and workflow scope
- `docs-ai/domains/TASK.md`: execution, hubs, kanban, steps
- `docs-ai/domains/ADMINISTRATION.md`: users, permissions, task catalogs
- `docs-ai/domains/CONFIGURATION.md`: base reference data
- `docs-ai/domains/AUDIT.md`: system-wide logging
- `docs-ai/domains/AUTH.md`: login and access lifecycle
- `docs-ai/domains/PROFILE.md`: authenticated user self-service
- `docs-ai/domains/PUBLIC.md`: public contact surface
- `docs-ai/domains/DASHBOARD.md`: authenticated entry page

Lookup by task:

- hierarchy or workflow changes: `ARCHITECTURE.md`, `SYSTEM_MAP.md`, `domains/ORGANIZATION.md`
- task execution changes: `ARCHITECTURE.md`, `domains/TASK.md`, `domains/ADMINISTRATION.md`
- access or identity changes: `domains/AUTH.md`, `domains/PROFILE.md`, `domains/ADMINISTRATION.md`
- coding style decisions: `CONVENTIONS.md`

## 10. Definition Of Done

A change is done when:

- hierarchy integrity is preserved
- service boundaries remain understandable
- no unnecessary duplication is introduced
- risk is known
- impact is justified
- performance impact was considered

## 11. Long-Term Goal

Keep the platform hierarchy-safe, scalable, and structurally coherent as the codebase grows.
