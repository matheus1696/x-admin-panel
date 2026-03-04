<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\AuditAssetDTO;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Services\Assets\AuditService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class AuditMobile extends Component
{
    use WithFileUploads, WithFlashMessage;

    protected AuditService $auditService;

    public string $searchCode = '';

    public ?string $notes = null;

    public $photo = null;

    public ?int $assetId = null;

    public function boot(AuditService $auditService): void
    {
        $this->auditService = $auditService;
    }

    public function mount(): void
    {
        Gate::authorize('audit', Asset::class);
    }

    public function searchAsset(): void
    {
        $data = $this->validate([
            'searchCode' => ['required', 'string'],
        ]);

        $asset = Asset::query()
            ->where('code', trim($data['searchCode']))
            ->first();

        $this->assetId = $asset?->id;

        if (! $asset) {
            $this->flashError(__('assets.audit_mobile.messages.not_found'));
        }
    }

    public function audit()
    {
        $data = $this->validate([
            'assetId' => ['required', 'exists:assets,id'],
            'photo' => ['required', 'image', 'max:5120'],
            'notes' => ['nullable', 'string'],
        ]);

        $path = $this->auditService->storeAuditPhoto($this->photo);

        $this->auditService->auditAsset(new AuditAssetDTO(
            assetId: (int) $data['assetId'],
            photoPath: $path,
            actorUserId: auth()->id(),
            notes: $data['notes'],
        ));

        $this->reset(['photo', 'notes', 'searchCode', 'assetId']);
        $this->flashSuccess(__('assets.audit_mobile.messages.success'));
    }

    public function render(): View
    {
        $asset = $this->assetId
            ? Asset::query()->with(['unit', 'sector'])->find($this->assetId)
            : null;

        return view('livewire.assets.audit-mobile', [
            'asset' => $asset,
        ])->layout('layouts.app');
    }
}
