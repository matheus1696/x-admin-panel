# X-AdminPanel — AI Engineering Context v1.0.26

## 1. System Purpose (WHY)

X-AdminPanel is an organizational intelligence platform focused on
visualizing corporate hierarchy and improving structural clarity.

This is NOT a generic admin CRUD.

Primary value:
→ Transform organizational data into visual understanding.

Core outcomes:
- hierarchical clarity
- strategic visibility
- centralized administration
- professional visual experience
- scalable organizational visualization

## 2. Architecture Overview

### Dashboard
Role: System orchestrator.

Responsibilities:
- aggregate data
- provide navigation
- summarize organizational structure

Never:
- contain heavy business logic.

---

### Organogram (CORE MODULE)
Strategic heart of the system.

Responsibilities:
- render hierarchy
- express relationships visually
- maintain structural coherence

Priority:
visual clarity > feature quantity.

---

### Wallfull
Extension of Organogram.

Purpose:
macro visualization and presentation mode.

Rules:
- fullscreen readability
- optimized rendering
- supports large hierarchy datasets

---

### Notifications
Event-driven communication layer.

Goals:
- governance
- traceability
- consistent institutional communication

## 3. Engineering Decision Rules

When implementing features, prioritize:

1. Hierarchy clarity
2. Visual readability
3. Performance
4. Maintainability
5. Feature expansion

If a solution increases UI complexity, reconsider it.

Organogram integrity must never be broken.
Wallfull must reuse Organogram logic.

Avoid:
- duplicated hierarchy logic
- UI-driven data structures
- controller-heavy logic

## 4. Laravel Architecture Rules

- Controllers are thin.
- Business logic lives in Services.
- Livewire manages UI state.
- Blade components remain declarative.
- Avoid query logic inside views.

## 5. Agent Behavior

Act as a senior Laravel architect.

You should:
- challenge poor architectural decisions
- prefer scalable solutions
- suggest refactors when needed
- protect long-term maintainability

Always consider system philosophy before coding.