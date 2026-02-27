# X-AdminPanel - AI Architecture & Evolution Contract

## 1. System Identity

X-AdminPanel is an Organizational Intelligence Platform.

It exists to transform corporate hierarchy data into
clear, strategic, and scalable visual understanding.

This is NOT:
- a generic CRUD admin
- a feature accumulator
- a UI-driven dashboard

It is:
- a hierarchy visualization engine
- a structural governance tool
- a centralized administrative intelligence layer

Primary Value Order:

1. Hierarchy Integrity
2. Visual Clarity
3. Architectural Integrity
4. Performance
5. Feature Expansion

If hierarchy integrity is compromised, the solution must be rejected.

---

# 1.1 Language & Encoding

Layout (UI) uses PT-BR with UTF-8 encoding.
Backend code, class names, and technical conventions remain in English and follow Laravel defaults.

---

# 1.2 Core Stack Guidelines

- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Blade + Livewire v3
- Interatividade: Livewire 3 + AlpineJS

---

# 2. Architectural Foundation

## 2.1 Architectural Hierarchy (Laravel)

Services
^
Controllers
^
Livewire
^
Blade

Rules:

- Services own all business logic.
- Controllers are thin.
- Livewire manages UI state only.
- Blade is declarative.
- No query logic inside views.
- No duplicated permission checks.
- No UI-driven domain rules.

If business rules appear outside Services, refactor.

---

# 3. Core Modules (Implemented)

## 3.1 Organogram (Strategic Core)

The Organogram is the heart of the system.

Responsibilities:
- Represent hierarchical relationships
- Maintain parent-child integrity
- Express structure visually

Non-negotiable:

- No duplicated hierarchy traversal logic
- No client-side structural rules
- No alternate hierarchy engines

Hierarchy logic must live in Services.

---

## 3.2 Dashboard

Role:
System orchestrator.

Must:
- Aggregate and summarize
- Provide navigation

Must not:
- Contain business rules
- Perform heavy structural logic

---

## 3.3 Workflow

Organizational process engine linked to OrganizationChart.

Must:
- Use WorkflowService / WorkflowStepService
- Keep step ordering and deadlines consistent
- Avoid embedding workflow rules in UI

---

## 3.4 Tasks (Operational Execution)

TaskHub is the container for tasks and steps.
TaskPage is the operational UI for execution.
Membership/share management is handled inside TaskPage (Membros tab).

Must:
- Use TaskService for task creation, kanban moves, and activity logging
- Preserve hub ownership and membership rules
- Keep audit trail consistent

---

# 4. Engineering Decision Doctrine

When implementing or evolving:

Prioritize in order:

1. Preserve hierarchy integrity
2. Preserve service boundaries
3. Preserve visual clarity
4. Improve performance
5. Expand features

If a solution increases UI complexity significantly, reconsider.

Avoid:

- Duplicated hierarchy logic
- Controller-heavy business rules
- Service fragmentation
- Tight module coupling
- Premature abstraction
- Overengineering

---

# 5. Evolution Modes

The system operates in one mode at a time.

## 5.1 Structural Evolution

Focus:
- Service boundary enforcement
- Business logic consolidation
- Removal of duplication
- Architectural consistency

Output must include:
- Issue description
- Architectural impact
- Severity (Critical / Structural / Improvement)
- Incremental fix proposal

---

## 5.2 Functional Evolution

Focus:
- Safe feature expansion
- Permission-aware growth
- Respect hierarchy integrity

Each proposal must include:
- Business value
- Architectural impact
- Required Service changes
- Migration complexity
- Risk level

---

## 5.3 Operational Evolution

Focus:
- Query efficiency
- N+1 detection
- Permission evaluation cost
- Tree traversal performance
- Caching opportunities
- Scalability limits

Each improvement must:
- Identify bottleneck
- Explain system impact
- Provide measurable gain expectation

---

# 6. Change Proposal Protocol

Before implementing:

1. Clearly define the problem.
2. Identify affected architectural layer.
3. Propose 2-3 strategies.
4. Compare tradeoffs.
5. Recommend safest path.

Avoid single-solution bias.

---

# 7. Code Review Rules

When reviewing code:

Check for:
- Business logic outside Services
- Responsibility leakage
- Duplicated domain logic
- Permission misuse
- N+1 queries
- Unsafe hierarchy manipulation
- Tight coupling

Classify findings:

- Critical (architecture break risk)
- Structural (design degradation risk)
- Improvement (refinement opportunity)

Do not rewrite stable modules unnecessarily.

---

# 8. System Protection Rules

Never:

- Break Organogram hierarchy integrity
- Duplicate hierarchy traversal logic
- Move domain rules into UI
- Bypass Services
- Introduce parallel service structures
- Increase click complexity without justification

If a request violates these rules:

- Explain why
- Propose safer alternative
- Refuse unsafe implementation if necessary

---

# 9. Red Flags

If detected, pause:

- Business logic inside Livewire
- Duplicate permission evaluation
- UI defining domain rules
- Feature expansion increasing structural coupling
- Performance degradation in tree traversal

---

# 10. Definition of Done

An evolution task is complete when:

- It respects architectural hierarchy
- Service boundaries remain intact
- No duplication introduced
- Organogram integrity preserved
- Risk documented
- Impact justified
- Performance impact considered

---

# 11. Long-Term Objective

Build a scalable, hierarchy-safe,
performance-aware organizational intelligence platform
capable of handling large enterprise structures
without architectural degradation.
