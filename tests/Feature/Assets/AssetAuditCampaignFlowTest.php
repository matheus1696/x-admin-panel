<?php

use App\Enums\Assets\AssetState;
use App\Livewire\Assets\AuditCampaignCreate;
use App\Livewire\Assets\AuditCampaignShow;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetAuditCampaign;
use App\Models\Assets\AssetAuditCampaignItem;
use App\Models\Assets\AssetAuditIssue;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;

function createAuditCampaignUser(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('audit campaign flow creates campaign audits item and finalizes', function () {
    $user = createAuditCampaignUser(['assets.audit', 'assets.view']);
    $this->actingAs($user);

    $assetA = Asset::create([
        'code' => 'AST-AUD-001',
        'description' => 'Notebook Auditoria',
        'state' => AssetState::IN_USE,
    ]);

    $assetB = Asset::create([
        'code' => 'AST-AUD-002',
        'description' => 'Monitor Auditoria',
        'state' => AssetState::IN_USE,
    ]);

    Livewire::test(AuditCampaignCreate::class)
        ->set('title', 'Auditoria piloto')
        ->call('save');

    $campaign = AssetAuditCampaign::query()->latest('id')->first();

    expect($campaign)->not->toBeNull()
        ->and($campaign->status)->toBe('IN_PROGRESS');

    $item = AssetAuditCampaignItem::query()
        ->where('asset_audit_campaign_id', $campaign->id)
        ->where('asset_id', $assetA->id)
        ->first();

    expect($item)->not->toBeNull();

    Livewire::test(AuditCampaignShow::class, ['uuid' => $campaign->uuid])
        ->call('openAuditItem', $item->id)
        ->set('auditStatus', 'DIVERGENCE')
        ->set('auditNotes', 'Ativo nao estava no local esperado')
        ->call('saveAuditItem')
        ->call('finalizeCampaign');

    $item->refresh();
    $campaign->refresh();

    expect($item->status)->toBe('DIVERGENCE')
        ->and($campaign->status)->toBe('CONCLUDED');

    expect(AssetAuditIssue::query()
        ->where('asset_audit_campaign_item_id', $item->id)
        ->where('status', 'OPEN')
        ->exists())->toBeTrue();

    expect($assetB->refresh()->code)->toBe('AST-AUD-002');
});

