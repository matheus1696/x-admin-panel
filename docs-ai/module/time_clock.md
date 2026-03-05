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

# Prompt Master — Controle de Ponto (TimeClock) Module
Laravel 12 + Livewire 3 + AlpineJS + Font Awesome + Spatie

UI: PT-BR (UTF-8) via lang files  
Code: English + camelCase  
Context: Existing X-AdminPanel module (must reuse layouts/components and follow docs-ai standards)

---

## 0) GLOBAL PROMPT (send this once to Codex before anything)

You are implementing a NEW MODULE inside an EXISTING Laravel 12 system (X-AdminPanel).

### Non-negotiables
- Reuse existing layouts, UI components, routes structure, and patterns already used in the system.
- Follow all standards described in docs-ai (naming, Livewire conventions, UI components, layout structure).
- Business rules MUST live in Services + Validations (no business logic in Livewire).
- Use Spatie Permission for access control and Laravel Policies for contextual authorization.
- Use AlpineJS ONLY for UI interactions (camera + geolocation only).
- Use Font Awesome using the system’s existing icon pattern.
- Code must be English, camelCase (classes PascalCase).
- UI must be PT-BR via lang/pt_BR (UTF-8). No hardcoded PT text in Blade/Livewire.
- DB writes must be transactional and concurrency-safe (lockForUpdate where needed).

### Append-only (MVP)
- time_clock_entries is append-only in MVP:
  - Never update or delete entries.
  - Future corrections/abonos must be implemented as NEW records in separate tables (out of scope).

### System Select standard (IMPORTANT)
- When using searchable selects in Livewire, use the system select-livewire component.
- Required: wire:model.live
- Do NOT close dropdown on focusout.
- Close only via click.outside, Esc, or selection.
- Use @mousedown.prevent on selection items.
- Do not mix native select with select-livewire.

### Required module naming
- UI module name (menus): Controle de Ponto
- Code module name: TimeClock
- Permission prefix: time_clock.*
- Translation file: lang/pt_BR/time_clock.php

### Database tables (snake_case)
- time_clock_entries
- time_clock_locations
- time_clock_settings (ONLY if the system stores module settings in DB; otherwise config-only)

### MVP scope (all points are approved)
Employee:
- Register a time clock entry (mobile or desktop)
- MUST capture photo
- MUST capture GPS (lat/lng + accuracy)
- Store device metadata (user_agent + ip)
- No approval workflow (all approved)

HR/Admin:
- List entries with filters
- View entry detail (photo + map + meta)
- Export CSV
- Reports:
  - entries by period
  - users without entry today

Out of scope (MVP):
- approvals
- abonos/corrections
- geofence blocking (locations are for future use only)

### Permissions (Spatie)
Create these permissions (or map to system naming if already defined):
- time_clock.register
- time_clock.view_own
- time_clock.view_any
- time_clock.reports.view
- time_clock.export
- time_clock.locations.manage
- time_clock.settings.manage

### Deliverable expectation (per stage)
- Generate code only for that stage
- Do not implement future stages early
- Provide: list of created/modified files with paths
- Include tests where specified

---

# 1) STAGE PROMPTS (send ONE per stage, sequentially)

## Stage 0 — Bootstrap, Config, Translations & Permissions
Implement:
1) Config
- config/time_clock.php with keys:
  - photo_required (bool, default true)
  - gps_required (bool, default true)
  - validate_location_enabled (bool, default false)
  - default_location_radius_meters (int, default 150)

2) Enum
- app/Enums/TimeClock/TimeClockEntryStatus.php:
  - OK
  - MISSING_GPS
  - MISSING_PHOTO

3) Seeder (Spatie)
- database/seeders/TimeClockPermissionsSeeder.php
  - create permissions:
    - time_clock.register
    - time_clock.view_own
    - time_clock.view_any
    - time_clock.reports.view
    - time_clock.export
    - time_clock.locations.manage
    - time_clock.settings.manage

4) Translations
- lang/pt_BR/time_clock.php (stub inicial)
  - menu.*
  - actions.*
  - fields.*
  - statuses.*
  - messages.*

Suggested translation keys (minimum):
- menu:
  - menu.controlle_de_ponto (or system naming pattern)
  - menu.register
  - menu.my_entries
  - menu.entries
  - menu.reports
  - menu.locations
  - menu.settings
- actions:
  - actions.register
  - actions.filter
  - actions.export
  - actions.view
  - actions.save
  - actions.cancel
- fields:
  - fields.date
  - fields.time
  - fields.photo
  - fields.latitude
  - fields.longitude
  - fields.accuracy
  - fields.user
  - fields.period
  - fields.status
- statuses:
  - statuses.ok
  - statuses.missing_gps
  - statuses.missing_photo
- messages:
  - messages.registered_success
  - messages.camera_denied
  - messages.gps_denied
  - messages.gps_unavailable
  - messages.upload_failed
  - messages.validation_failed

5) Tests
- config loads
- enum values exist
- permissions seeder creates permissions

Constraints:
- Do NOT create migrations yet.
- Do NOT create Livewire components yet.

Output:
- List created/modified files with paths.

---

## Stage 1 — Migrations & Models (Core Data)
Implement migrations + models for:
- time_clock_entries
- time_clock_locations
- time_clock_settings ONLY if the system uses DB settings (otherwise skip)

### time_clock_entries columns
- id (system standard: ULID/UUID/bigint)
- user_id (FK users)
- occurred_at (timestamp)
- photo_path (string)
- latitude (decimal 10,7)
- longitude (decimal 10,7)
- accuracy (float)
- device_meta (json: ip, user_agent, optional device hints)
- status (string)
- location_id (nullable FK time_clock_locations)
- timestamps

Indexes:
- (user_id, occurred_at)
- (occurred_at)
- (status, occurred_at)

### time_clock_locations columns
- id
- name
- latitude (decimal 10,7)
- longitude (decimal 10,7)
- radius_meters (int)
- active (bool)
- timestamps

Models:
- TimeClockEntry
- TimeClockLocation

Relations:
- entry -> user
- entry -> location (nullable)

Tests:
- migration runs
- model relations sanity test

Constraints:
- No Services yet.
- No Livewire yet.

Output:
- List created/modified files with paths.

---

## Stage 2 — DTO Layer
Implement DTOs in app/DTOs/TimeClock/:

- RegisterTimeClockEntryDTO with properties:
  - userId
  - occurredAt (CarbonImmutable)
  - photo (UploadedFile)
  - latitude (float|null)
  - longitude (float|null)
  - accuracy (float|null)
  - deviceMeta (array)
  - status (string)
  - locationId (nullable)

Requirements:
- English, camelCase properties
- Constructors with named parameters
- Strong typing where possible

Output:
- List created/modified files with paths.

---

## Stage 3 — Validation Layer (Business Rules)
Implement validators in app/Validations/TimeClock/:

- PhotoRequiredValidator::validateOrFail(?UploadedFile $photo)
- GpsRequiredValidator::validateOrFail(?float $lat, ?float $lng, ?float $accuracy)
- RegisterRateLimitValidator::validateOrFail($userId) (optional; minimal)
- LocationWithinRadiusValidator (ONLY if validate_location_enabled true; do not block MVP flows)

Exception:
- TimeClockValidationException (if no system standard exists)

Tests:
- missing photo behavior (according to config)
- missing gps behavior (according to config)
- happy path

Output:
- List created/modified files with paths.

---

## Stage 4 — Services Layer (Transactional Core)
Implement services in app/Services/TimeClock/:

### TimeClockEntryService
- register(RegisterTimeClockEntryDTO $dto): TimeClockEntry
  - DB::transaction()
  - run validators
  - store photo using system storage/upload conventions
  - create time_clock_entries record (append-only)
  - store device_meta (ip + user_agent)
  - status should be OK when requirements satisfied

Concurrency:
- if checking latest entry per user, use lockForUpdate where needed

Tests:
- creates entry + stores photo
- stores device_meta
- missing requirements handled per validation/config

Output:
- List created/modified files with paths.

---

## Stage 5 — Policies (Spatie + Context)
Implement:
- TimeClockEntryPolicy
- TimeClockLocationPolicy
- Register policies using system standard

TimeClockEntryPolicy methods:
- register
- viewOwn
- viewAny
- view
- export
- viewReports

TimeClockLocationPolicy methods:
- manage

Rules:
- Owner can view own entries
- HR/Admin can viewAny/view/export/reports
- Use Spatie permission checks

Tests:
- allow/deny coverage

Output:
- List created/modified files with paths.

---

## Stage 6 — Livewire UI: Registrar Ponto (Mobile/Desktop)
Implement Livewire component:
- RegisterEntry

UI requirements:
- Use X-AdminPanel layout/components.
- Use Font Awesome icons via system pattern:
  - fa-camera, fa-location-dot, fa-clock, fa-check
- Use AlpineJS for:
  - camera preview + capture (getUserMedia)
  - geolocation capture (navigator.geolocation)
  - submit captured file + coords to Livewire action
- No hardcoded PT; use lang/pt_BR/time_clock.php

Authorization:
- time_clock.register via policy/permission

Output:
- List created/modified files with paths.

---

## Stage 7 — Livewire UI: Meus Registros
Implement:
- MyEntries

Requirements:
- pagination
- simple date filter
- no edit/delete actions
- authorize viewOwn/view

Output:
- List created/modified files with paths.

---

## Stage 8 — Livewire UI: Registros (RH) + Detalhe
Implement:
- EntriesIndex
- EntryShow

EntriesIndex requirements:
- filters:
  - dateFrom/dateTo
  - user (select-livewire, wire:model.live)
  - status
- use system table components

EntryShow requirements:
- photo preview
- map display following system conventions (leaflet if system uses; otherwise a maps link)
- meta display: accuracy, ip, user_agent, occurred_at

Authorize:
- viewAny/view

Output:
- List created/modified files with paths.

---

## Stage 9 — Locations CRUD + Settings (optional UI)
Implement:
- LocationsIndex (CRUD)
- SettingsForm ONLY if system uses DB settings; otherwise config-only and skip UI

Locations fields:
- name
- latitude
- longitude
- radiusMeters
- active

Authorization:
- time_clock.locations.manage
- time_clock.settings.manage (if SettingsForm exists)

Output:
- List created/modified files with paths.

---

## Stage 10 — Reports & Export
Implement:
- ReportsIndex
  - Report A: Entries by period (filters + list + totals)
  - Report B: Users without entry today
- CSV export using system export pattern

Create:
- TimeClockReportService

Authorization:
- time_clock.reports.view
- time_clock.export

Tests:
- report queries tests
- export format tests (headers + columns)

Output:
- List created/modified files with paths.

---

## Stage 11 — Hardening (Optional)
Implement:
- logging context improvements (userId, entryId, occurredAt)
- rate limit finalization (if enabled)
- photo storage security review (private storage + signed access if system requires)
- index review + query optimization

Output:
- List created/modified files with paths.
- Summary of hardening changes.

---