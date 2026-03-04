# Prompt Master — TimeClock Module (Laravel 12 + Livewire 3 + AlpineJS + Font Awesome + Spatie)
UI: PT-BR (UTF-8) via lang files  
Code: English + camelCase  
Context: Existing system module (must reuse layouts/components and follow docs-ai standards)

---

## 0) GLOBAL PROMPT (send this once to Codex before anything)

You are implementing a NEW MODULE inside an EXISTING Laravel 12 system.

### Non-negotiables
- Reuse existing layouts, UI components, and patterns already used in the system.
- Follow all standards described in docs-ai (naming, Livewire conventions, UI components, layout structure).
- Business rules must live in Services + Validations (no business logic in Livewire).
- Use Spatie Permission for access control and Laravel Policies for contextual authorization.
- Use AlpineJS only for UI interactions.
- Use Font Awesome using the system’s existing icon pattern.
- Code must be English, camelCase (classes PascalCase).
- UI must be PT-BR via lang/pt_BR (UTF-8). No hardcoded PT text in Blade/Livewire.
- DB writes must be transactional and concurrency-safe (lockForUpdate where needed).
- time_clock_entries is append-only (no update/delete).

### Required module name
- Module name: TimeClock
- UI name: Controle de Ponto

### Required folder locations
Create/Use these locations (adjust only if docs-ai/system has different canonical paths):
- Services: `app/Services/TimeClock/`
- Validations: `app/Validations/TimeClock/`
- DTOs: `app/DTOs/TimeClock/`
- Jobs: `app/Jobs/TimeClock/`
- Policies: `app/Policies/TimeClock/`
- Livewire Components: `app/Livewire/TimeClock/` (or system standard)
- Views: `resources/views/livewire/time-clock/` (or system standard)
- Translations: `lang/pt_BR/time_clock.php`

### Permissions (Spatie)
Create these permissions (or map to existing naming pattern if already defined):
- time_clock.register
- time_clock.view_own
- time_clock.view_any
- time_clock.reports.view
- time_clock.export
- time_clock.locations.manage
- time_clock.settings.manage

### Entry Status
Time clock entries can have the following statuses:
- OK
- MISSING_GPS
- MISSING_PHOTO

Entries with missing information should still be recorded but flagged with the appropriate status.

### Database tables (snake_case)
- time_clock_entries
- time_clock_locations
- time_clock_settings (optional depending on system configuration standard)

### Entry rules
- Every time clock registration must create a new record in `time_clock_entries`.
- Entries must never be edited or deleted.
- Each entry must store:
  - user_id
  - occurred_at
  - photo_path
  - latitude
  - longitude
  - accuracy
  - device_meta (ip, user_agent)
  - status

### Deliverable expectation
For each stage:
- Generate code only for that stage
- Include tests where specified
- Do not implement future stages early
- Keep changes aligned with the existing system conventions

---

# 1) STAGE PROMPTS (send ONE per stage, sequentially)