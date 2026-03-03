# Prompt Master — Assets Module (Laravel 12 + Livewire 3 + AlpineJS + Font Awesome + Spatie)
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
- asset_events is append-only (no update/delete).

### Required module name
- Module name: Assets

### Required folder locations
Create/Use these locations (adjust only if docs-ai/system has different canonical paths):
- Services: `app/Services/Assets/`
- Validations: `app/Validations/Assets/`
- DTOs: `app/DTOs/Assets/`
- Jobs: `app/Jobs/Assets/`
- Policies: `app/Policies/Assets/`
- Livewire Components: `app/Livewire/Assets/` (or system standard)
- Views: `resources/views/livewire/assets/` (or system standard)
- Translations: `lang/pt_BR/assets.php`

### Permissions (Spatie)
Create these permissions (or map to existing naming pattern if already defined):
- assets.view
- assets.invoices.manage
- assets.stock.receive
- assets.release
- assets.transfer
- assets.audit
- assets.state.change
- assets.return
- assets.reports.view
- assets.bulk.execute

### State Machine
Asset states:
- IN_STOCK, RELEASED, IN_USE, MAINTENANCE, DAMAGED, RETURNED_TO_PATRIMONY

Allowed transitions:
- IN_STOCK → RELEASED
- RELEASED → IN_USE
- RELEASED → MAINTENANCE
- IN_USE → MAINTENANCE
- MAINTENANCE → RELEASED
- RELEASED → RETURNED_TO_PATRIMONY
RETURNED_TO_PATRIMONY is terminal.

### Database tables (snake_case)
- asset_invoices
- asset_invoice_items
- assets
- asset_events
- bulk_operations
- bulk_operation_items

### Event rules
- Every change to assets.state/unit_id/sector_id MUST create an asset_event.
- asset_events must never be edited or deleted.

### Deliverable expectation
For each stage:
- Generate code only for that stage
- Include tests where specified
- Do not implement future stages early
- Keep changes aligned with the existing system conventions

---

# 1) STAGE PROMPTS (send ONE per stage, sequentially)

## Stage 0 — Bootstrap & Permissions
Implement:
- `config/assets.php` with keys:
  - stock_default_unit_id
  - patrimony_unit_id
- Enums:
  - `app/Enums/Assets/AssetState.php` (or system standard)
  - `app/Enums/Assets/AssetEventType.php`
- Spatie permissions seeder:
  - `database/seeders/AssetsPermissionsSeeder.php`
- Translation stub:
  - `lang/pt_BR/assets.php` with initial keys for module menus and actions (PT-BR).
- Basic tests verifying:
  - config loads
  - enums contain required values
  - permissions seeder creates the permissions

Constraints:
- Do not create migrations yet.
- Do not create Livewire components yet.

Output:
- List created/modified files with paths.

---

## Stage 1 — Migrations & Models (Core Data)
Implement migrations + models for:
- asset_invoices
- asset_invoice_items
- assets
- asset_events
- bulk_operations
- bulk_operation_items

Requirements:
- Follow system migration and auditing conventions.
- Use ULID if system standard (otherwise UUID).
- Add indexes:
  - assets: (state, unit_id, sector_id), (invoice_item_id), unique(code)
  - asset_events: (asset_id, created_at), (type, created_at)
  - bulk_operation_items: (bulk_operation_id, status)
- Define Eloquent relations for all models.
- Ensure UTF-8 (utf8mb4).

Tests:
- Migration runs
- Basic model relations sanity test

Constraints:
- No Services yet.
- No Livewire yet.

Output:
- List created/modified files with paths.

---

## Stage 2 — DTO Layer
Implement DTOs (English, camelCase properties) in `app/DTOs/Assets/`:
- CreateInvoiceDTO
- UpsertInvoiceItemDTO
- ReceiveStockDTO
- ReleaseAssetDTO
- TransferAssetDTO
- AuditAssetDTO
- ChangeAssetStateDTO
- ReturnToPatrimonyDTO
- CreateBulkOperationDTO

Requirements:
- Strong typing where possible
- Constructors with named parameters

Tests:
- Basic instantiation tests (optional but recommended)

Output:
- List created/modified files with paths.

---

## Stage 3 — Validation Layer (Business Rules)
Implement validators in `app/Validations/Assets/`:
- AllowedStateTransitionValidator
- CanReceiveStockValidator
- CanReleaseAssetValidator
- CanTransferAssetValidator
- CanAuditAssetValidator
- CanChangeStateValidator
- CanReturnToPatrimonyValidator
- SectorBelongsToUnitValidator (only if Units/Sectors enforce that relation in system)

Requirements:
- Each validator should expose a clear method, e.g. `validateOrFail(...)`
- Use domain exceptions (create `AssetsValidationException` if standard exists)

Tests:
- Cover allowed and blocked transitions
- Cover sector nullable behavior

Output:
- List created/modified files with paths.

---

## Stage 4 — Services Layer (Transactional Core)
Implement services in `app/Services/Assets/`:

### InvoiceService
- createInvoice(CreateInvoiceDTO): AssetInvoice
- addOrUpdateItem(UpsertInvoiceItemDTO): AssetInvoiceItem
- deleteItem(itemId): void

### StockService
- receiveStock(ReceiveStockDTO): array
  - Create N assets (one per quantity)
  - Set initial state IN_STOCK
  - unit_id defaults to config stock_default_unit_id
  - sector_id null
  - Create event STOCK_RECEIVED for each created asset

### AssetOperationService
- releaseAsset(ReleaseAssetDTO): void
- transferAsset(TransferAssetDTO): void
- changeAssetState(ChangeAssetStateDTO): void
- returnToPatrimony(ReturnToPatrimonyDTO): void

### AuditService
- auditAsset(AuditAssetDTO): void
  - store photo according to system file storage pattern
  - create AUDITED event

### BulkOperationService (only creation & dispatch stub here)
- createOperation(CreateBulkOperationDTO): BulkOperation
- addItems(op, assetIds): void
- dispatch(op): void

Requirements:
- All write operations use DB::transaction()
- For operations that update an existing asset: lock the row (lockForUpdate)
- Always create asset_events for state/unit/sector changes
- Respect sector nullable
- Respect state transition matrix

Tests:
- Unit tests for each service happy path + invalid rules
- Ensure events are created

Output:
- List created/modified files with paths.

---

## Stage 5 — Policies (Spatie + Context Rules)
Implement:
- `app/Policies/Assets/AssetPolicy.php` (or system standard)
- Register policy in AuthServiceProvider if needed

Methods:
- viewAny/view
- manageInvoices
- receiveStock
- release
- transfer
- audit
- changeState
- return
- viewReports
- bulkExecute

Requirements:
- Use Spatie permission checks
- Add contextual constraints if system requires (e.g., unit scope)

Tests:
- Policy tests for allow/deny

Output:
- List created/modified files with paths.

---

## Stage 6 — Livewire UI: Invoices
Implement Livewire components for Invoices using system layout + components:
- InvoiceIndex
- InvoiceForm (create/edit)
- InvoiceShow (details + items)

Requirements:
- UI in PT-BR using translation keys (lang/pt_BR/assets.php)
- Use existing form components and select components (docs-ai standard)
- Use Font Awesome icons following system pattern
- authorize with policies/permissions

Tests:
- Feature tests for basic invoice flow (optional if system standard supports)

Output:
- List created/modified files with paths.

---

## Stage 7 — Livewire UI: Stock Entry
Implement:
- ReceiveStockForm embedded in InvoiceShow OR dedicated route (choose system standard)

Requirements:
- Calls StockService::receiveStock
- Shows result summary (qty created + link to Assets list)
- PT-BR labels via lang files
- authorize receiveStock

Output:
- List created/modified files with paths.

---

## Stage 8 — Livewire UI: Assets List & Detail
Implement:
- AssetsIndex: filters + pagination + selection (for bulk)
- AssetShow: details + timeline paginated

Requirements:
- Filters use existing select-livewire patterns (wire:model.live) if standard
- Timeline paginated, “load more” allowed
- PT-BR labels via lang files
- authorize viewAny/view

Output:
- List created/modified files with paths.

---

## Stage 9 — Livewire UI: Operations
Implement forms/components:
- ReleaseAssetForm
- TransferAssetForm
- ChangeStateForm
- ReturnToPatrimonyForm

Requirements:
- Use modals and inputs per system standard
- Call AssetOperationService methods
- PT-BR translations
- Sector nullable
- authorize each action

Output:
- List created/modified files with paths.

---

## Stage 10 — Livewire UI: Audit Mobile (Photo)
Implement:
- AuditMobile component

Requirements:
- Search/scan by asset code
- Upload photo using system storage standard
- Create audit event via AuditService
- Mobile-friendly layout using system components
- PT-BR translations
- authorize audit

Output:
- List created/modified files with paths.

---

## Stage 11 — Bulk Operations (Queue)
Implement:
- Job: `app/Jobs/Assets/ProcessBulkOperationJob.php`
- UI: BulkOperationProgress
- Integration: create bulk ops from selection on AssetsIndex

Requirements:
- Process items in chunks of 200
- Track per-item OK/FAIL and error messages
- Mark bulk status DONE/PARTIAL/FAILED
- Use services for actual operations
- Safe retries where possible
- PT-BR translations
- authorize bulk.execute

Output:
- List created/modified files with paths.

---

## Stage 12 — Reports
Implement report components:
- AssetsByUnit
- AssetsByState
- TransfersByPeriod
- AuditsByPeriod
- PurchasesByPeriod

Requirements:
- Use AssetsReportService
- Use system filter UI patterns
- Export using system export standard (Excel first, PDF if standard exists)
- PT-BR translations
- authorize reports.view

Output:
- List created/modified files with paths.

---

## Stage 13 — Hardening
Implement:
- Idempotency for critical operations (release/transfer/return)
- Concurrency tests (two updates same asset)
- Logging improvements with context
- Index review / query optimization

Output:
- List created/modified files with paths.
- Summary of hardening changes.