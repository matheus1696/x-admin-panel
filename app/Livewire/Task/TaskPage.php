<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStatus;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Task\Task;
use App\Models\Task\TaskStep;
use App\Services\Task\TaskCategoryService;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Support\Notifications\InteractsWithSystemNotifications;
use App\Validation\Task\TaskCategoryRules;
use App\Validation\Task\TaskRules;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TaskPage extends Component
{
    use InteractsWithSystemNotifications;
    use Modal, WithFlashMessage, WithPagination;

    protected TaskService $taskService;

    protected TaskCategoryService $taskCategoryService;

    protected TaskStatusService $taskStatusService;

    protected TaskStepStatusService $taskStepStatusService;

    public array $filters = [
        'title' => '',
        'organization_id' => '',
        'user_id' => '',
        'task_category_id' => '',
        'task_status_id' => '',
        'task_priority_id' => '',
        'is_overdue' => 'all',
        'perPage' => 50,
    ];

    public array $stepKanbanFilters = [
        'task_id' => '',
        'organization_id' => '',
        'user_id' => '',
    ];

    public Collection $users;

    public Collection $taskCategories;

    public Collection $taskHubCategories;

    public Collection $taskHubTaskStatuses;

    public Collection $taskHubTaskStepStatuses;

    public Collection $taskPriorities;

    public Collection $taskStatuses;

    public Collection $organizations;

    public Collection $taskStepStatuses;

    public string $taskHubId;

    public int $taskHubInternalId;

    public int $taskHubOwnerId;

    public ?int $selectedTaskId = null;

    public ?int $selectedStepId = null;

    public ?string $title = null;

    public ?int $user_id = null;

    public ?int $task_category_id = null;

    public ?int $taskHubCategoryId = null;

    public ?int $task_priority_id = null;

    public ?int $task_status_id = null;

    public ?string $step_title = null;

    public ?int $step_user_id = null;

    public ?int $organization_id = null;

    public ?int $step_task_priority_id = null;

    public ?int $task_step_status_id = null;

    public ?int $member_user_id = null;

    public ?int $member_organization_id = null;

    /**
     * @var array<int>
     */
    public array $expandedOrganizationIds = [];

    public ?int $pendingStepMoveStepId = null;

    public ?int $pendingStepMoveFromStatusId = null;

    public ?int $pendingStepMoveToStatusId = null;

    /**
     * @var array<int>
     */
    public array $pendingStepMoveTargetOrder = [];

    public string $stepCompletionComment = '';

    public ?string $pendingStepMoveReasonType = null;

    public string $taskHubCategoryTitle = '';

    public ?string $taskHubCategoryDescription = null;

    public ?int $taskHubTaskStatusId = null;

    public string $taskHubTaskStatusTitle = '';

    public string $taskHubTaskStatusPalette = 'gray';

    public ?int $taskHubTaskStepStatusId = null;

    public string $taskHubTaskStepStatusTitle = '';

    public string $taskHubTaskStepStatusPalette = 'gray';

    public function boot(
        TaskService $taskService,
        TaskCategoryService $taskCategoryService,
        TaskStatusService $taskStatusService,
        TaskStepStatusService $taskStepStatusService
    ): void {
        $this->taskService = $taskService;
        $this->taskCategoryService = $taskCategoryService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusService = $taskStepStatusService;
    }

    protected function setDefaults(): void
    {
        $this->task_priority_id = $this->taskPriorities->firstWhere('is_default', true)?->id;
        $this->task_status_id = $this->taskStatuses->firstWhere('is_default', true)?->id;
    }

    protected function setStepDefaults(): void
    {
        $this->step_task_priority_id = $this->taskPriorities->firstWhere('is_default', true)?->id;
        $this->task_step_status_id = $this->taskStepStatuses->firstWhere('is_default', true)?->id;
    }

    protected function refreshTaskCategories(): void
    {
        $this->taskCategories = $this->taskCategoryService->visibleForHub($this->taskHubInternalId, true);
        $this->taskHubCategories = $this->taskCategoryService->localForHub($this->taskHubInternalId);

        if ($this->task_category_id !== null && ! $this->taskCategories->contains('id', $this->task_category_id)) {
            $this->task_category_id = null;
        }
    }

    protected function refreshTaskStatuses(): void
    {
        $this->taskStatuses = $this->taskStatusService->index($this->taskHubInternalId);
        $this->taskStepStatuses = $this->taskStepStatusService->index($this->taskHubInternalId);
        $this->taskHubTaskStatuses = $this->taskStatusService->index($this->taskHubInternalId, false);
        $this->taskHubTaskStepStatuses = $this->taskStepStatusService->index($this->taskHubInternalId, false);
    }

    public function mount(string $uuid): void
    {
        $userId = Auth::user()->id;

        $taskHub = $this->taskService->findAccessibleHub($uuid, $userId);

        $this->taskHubId = $taskHub->uuid;
        $this->taskHubInternalId = $taskHub->id;
        $this->taskHubOwnerId = (int) $taskHub->owner_id;
        $this->users = User::orderBy('name')->get();
        $this->organizations = OrganizationChart::orderBy('order')->get();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->refreshTaskStatuses();
        $this->refreshTaskCategories();
        $this->setStepDefaults();
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function resetListFilters(): void
    {
        $this->filters = [
            'title' => '',
            'organization_id' => '',
            'user_id' => '',
            'task_category_id' => '',
            'task_status_id' => '',
            'task_priority_id' => '',
            'is_overdue' => 'all',
            'perPage' => $this->filters['perPage'] ?? 50,
        ];

        $this->resetPage();
    }

    public function resetStepKanbanFilters(): void
    {
        $this->stepKanbanFilters = [
            'task_id' => '',
            'organization_id' => '',
            'user_id' => '',
        ];
    }

    public function resetForm(): void
    {
        $this->reset('title', 'user_id', 'task_category_id', 'task_priority_id', 'task_status_id');
    }

    public function enableCreateTask(): void
    {
        $this->resetForm();
        $this->setDefaults();
        $this->openModal('modal-task-create');
    }

    public function cancelCreateTask(): void
    {
        $this->resetForm();
        $this->setDefaults();
        $this->closeModal();
    }

    public function resetStepForm(): void
    {
        $this->reset('step_title', 'step_user_id', 'organization_id', 'step_task_priority_id', 'task_step_status_id');
    }

    public function cancelCreateTaskStep(): void
    {
        $this->resetStepForm();
        $this->setStepDefaults();
    }

    public function updatedOrganizationId(): void
    {
        $this->step_user_id = null;
    }

    public function storeTask(): void
    {
        $accessUserIds = $this->taskService
            ->accessUsers($this->taskHubId)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $availableCategoryIds = $this->taskCategories
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $data = $this->validate(array_merge(TaskRules::store(), [
            'user_id' => ['nullable', Rule::in($accessUserIds)],
            'task_category_id' => ['nullable', Rule::in($availableCategoryIds)],
        ]));

        $this->taskService->create($this->taskHubId, $data);

        $this->resetForm();
        $this->closeModal();
        $this->flashSuccess('Tarefa criada com sucesso.');
    }

    public function createTaskCategory(): void
    {
        $this->reset('taskHubCategoryId', 'taskHubCategoryTitle', 'taskHubCategoryDescription');
        $this->openModal('modal-task-category-create');
    }

    public function storeTaskCategory(): void
    {
        $data = $this->validate(TaskCategoryRules::store($this->taskHubInternalId));

        $category = $this->taskCategoryService->createForHub(
            $this->taskHubInternalId,
            (int) Auth::id(),
            [
                'title' => $data['taskHubCategoryTitle'],
                'description' => $data['taskHubCategoryDescription'] ?? null,
            ]
        );

        if (! $category) {
            $this->flashError('Apenas o proprietário pode gerenciar as categorias deste ambiente.');

            return;
        }

        $this->refreshTaskCategories();
        $this->closeModal();
        $this->flashSuccess('Categoria do ambiente criada com sucesso.');
    }

    public function editTaskCategory(int $id): void
    {
        $category = $this->taskHubCategories->firstWhere('id', $id);

        if (! $category) {
            abort(404);
        }

        $this->taskHubCategoryId = $category->id;
        $this->taskHubCategoryTitle = $category->title;
        $this->taskHubCategoryDescription = $category->description;

        $this->openModal('modal-task-category-edit');
    }

    public function updateTaskCategory(): void
    {
        if (! $this->taskHubCategoryId) {
            return;
        }

        $data = $this->validate(TaskCategoryRules::store($this->taskHubInternalId, $this->taskHubCategoryId));

        $updated = $this->taskCategoryService->updateForHub(
            $this->taskHubInternalId,
            (int) Auth::id(),
            $this->taskHubCategoryId,
            [
                'title' => $data['taskHubCategoryTitle'],
                'description' => $data['taskHubCategoryDescription'] ?? null,
            ]
        );

        if (! $updated) {
            $this->flashError('Apenas o proprietário pode gerenciar as categorias deste ambiente.');

            return;
        }

        $this->refreshTaskCategories();
        $this->closeModal();
        $this->flashSuccess('Categoria do ambiente atualizada com sucesso.');
    }

    public function toggleTaskCategoryStatus(int $id): void
    {
        $updated = $this->taskCategoryService->toggleStatusForHub(
            $this->taskHubInternalId,
            (int) Auth::id(),
            $id
        );

        if (! $updated) {
            $this->flashError('Apenas o proprietário pode gerenciar as categorias deste ambiente.');

            return;
        }

        $this->refreshTaskCategories();
        $this->flashSuccess('Status da categoria atualizado com sucesso.');
    }

    public function createTaskStatus(): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $this->reset('taskHubTaskStatusId', 'taskHubTaskStatusTitle');
        $this->taskHubTaskStatusPalette = 'gray';
        $this->openModal('modal-task-status-create');
    }

    public function storeTaskStatus(): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $data = $this->validate([
            'taskHubTaskStatusTitle' => [
                'required',
                'string',
                'min:3',
                'max:80',
                Rule::unique('task_statuses', 'title')->where(fn ($query) => $query->where('task_hub_id', $this->taskHubInternalId)),
            ],
            'taskHubTaskStatusPalette' => ['required', Rule::in(array_keys($this->statusPaletteOptions()))],
        ]);

        [$color, $tailwind] = $this->paletteToStatusStyle($data['taskHubTaskStatusPalette']);

        $this->taskStatusService->createForHub($this->taskHubInternalId, [
            'title' => trim($data['taskHubTaskStatusTitle']),
            'color' => $color,
            'color_code_tailwind' => $tailwind,
            'is_default' => false,
            'is_active' => true,
        ]);

        $this->refreshTaskStatuses();
        $this->closeModal();
        $this->flashSuccess('Status de tarefa criado com sucesso.');
    }

    public function editTaskStatus(int $id): void
    {
        $status = $this->taskHubTaskStatuses->firstWhere('id', $id);

        if (! $status) {
            abort(404);
        }

        $this->taskHubTaskStatusId = $status->id;
        $this->taskHubTaskStatusTitle = $status->title;
        $this->taskHubTaskStatusPalette = $status->color ?: 'gray';
        $this->openModal('modal-task-status-edit');
    }

    public function updateTaskStatus(): void
    {
        if (! $this->canManageEnvironmentSettings() || ! $this->taskHubTaskStatusId) {
            return;
        }

        $status = $this->taskStatusService->find($this->taskHubTaskStatusId, $this->taskHubInternalId);

        if ($this->isTaskTerminalStatus($status->title)) {
            $this->flashError('Status de conclusão e cancelamento não podem ser alterados.');

            return;
        }

        $data = $this->validate([
            'taskHubTaskStatusTitle' => [
                'required',
                'string',
                'min:3',
                'max:80',
                Rule::unique('task_statuses', 'title')
                    ->where(fn ($query) => $query->where('task_hub_id', $this->taskHubInternalId))
                    ->ignore($status->id),
            ],
            'taskHubTaskStatusPalette' => ['required', Rule::in(array_keys($this->statusPaletteOptions()))],
        ]);

        [$color, $tailwind] = $this->paletteToStatusStyle($data['taskHubTaskStatusPalette']);

        $this->taskStatusService->update($status->id, [
            'title' => trim($data['taskHubTaskStatusTitle']),
            'color' => $color,
            'color_code_tailwind' => $tailwind,
        ], $this->taskHubInternalId);

        $this->refreshTaskStatuses();
        $this->closeModal();
        $this->flashSuccess('Status de tarefa atualizado com sucesso.');
    }

    public function toggleTaskStatus(int $id): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $status = $this->taskStatusService->find($id, $this->taskHubInternalId);

        if ($this->isTaskTerminalStatus($status->title)) {
            $this->flashError('Status de conclusão e cancelamento não podem ser inativados.');

            return;
        }

        $this->taskStatusService->update($status->id, [
            'is_active' => ! (bool) $status->is_active,
        ], $this->taskHubInternalId);

        $this->refreshTaskStatuses();
        $this->flashSuccess('Status de tarefa atualizado com sucesso.');
    }

    public function setTaskStatusDefault(int $id): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $status = $this->taskStatusService->find($id, $this->taskHubInternalId);

        if ($this->isTaskTerminalStatus($status->title)) {
            $this->flashError('Status de conclusão e cancelamento não podem ser padrão.');

            return;
        }

        $this->taskStatusService->setDefaultForHub($this->taskHubInternalId, $status->id);

        $this->refreshTaskStatuses();
        $this->flashSuccess('Status padrão de tarefa atualizado com sucesso.');
    }

    public function createTaskStepStatus(): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $this->reset('taskHubTaskStepStatusId', 'taskHubTaskStepStatusTitle');
        $this->taskHubTaskStepStatusPalette = 'gray';
        $this->openModal('modal-task-step-status-create');
    }

    public function storeTaskStepStatus(): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $data = $this->validate([
            'taskHubTaskStepStatusTitle' => [
                'required',
                'string',
                'min:3',
                'max:80',
                Rule::unique('task_step_statuses', 'title')->where(fn ($query) => $query->where('task_hub_id', $this->taskHubInternalId)),
            ],
            'taskHubTaskStepStatusPalette' => ['required', Rule::in(array_keys($this->statusPaletteOptions()))],
        ]);

        [$color, $tailwind] = $this->paletteToStatusStyle($data['taskHubTaskStepStatusPalette']);

        $this->taskStepStatusService->createForHub($this->taskHubInternalId, [
            'title' => trim($data['taskHubTaskStepStatusTitle']),
            'color' => $color,
            'color_code_tailwind' => $tailwind,
            'is_default' => false,
            'is_active' => true,
        ]);

        $this->refreshTaskStatuses();
        $this->closeModal();
        $this->flashSuccess('Status de etapa criado com sucesso.');
    }

    public function editTaskStepStatus(int $id): void
    {
        $status = $this->taskHubTaskStepStatuses->firstWhere('id', $id);

        if (! $status) {
            abort(404);
        }

        $this->taskHubTaskStepStatusId = $status->id;
        $this->taskHubTaskStepStatusTitle = $status->title;
        $this->taskHubTaskStepStatusPalette = $status->color ?: 'gray';
        $this->openModal('modal-task-step-status-edit');
    }

    public function updateTaskStepStatus(): void
    {
        if (! $this->canManageEnvironmentSettings() || ! $this->taskHubTaskStepStatusId) {
            return;
        }

        $status = $this->taskStepStatusService->find($this->taskHubTaskStepStatusId, $this->taskHubInternalId);

        if ($this->isTaskStepTerminalStatus($status->title)) {
            $this->flashError('Status de conclusão e cancelamento não podem ser alterados.');

            return;
        }

        $data = $this->validate([
            'taskHubTaskStepStatusTitle' => [
                'required',
                'string',
                'min:3',
                'max:80',
                Rule::unique('task_step_statuses', 'title')
                    ->where(fn ($query) => $query->where('task_hub_id', $this->taskHubInternalId))
                    ->ignore($status->id),
            ],
            'taskHubTaskStepStatusPalette' => ['required', Rule::in(array_keys($this->statusPaletteOptions()))],
        ]);

        [$color, $tailwind] = $this->paletteToStatusStyle($data['taskHubTaskStepStatusPalette']);

        $this->taskStepStatusService->update($status->id, [
            'title' => trim($data['taskHubTaskStepStatusTitle']),
            'color' => $color,
            'color_code_tailwind' => $tailwind,
        ], $this->taskHubInternalId);

        $this->refreshTaskStatuses();
        $this->closeModal();
        $this->flashSuccess('Status de etapa atualizado com sucesso.');
    }

    public function toggleTaskStepStatus(int $id): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $status = $this->taskStepStatusService->find($id, $this->taskHubInternalId);

        if ($this->isTaskStepTerminalStatus($status->title)) {
            $this->flashError('Status de conclusão e cancelamento não podem ser inativados.');

            return;
        }

        $this->taskStepStatusService->update($status->id, [
            'is_active' => ! (bool) $status->is_active,
        ], $this->taskHubInternalId);

        $this->refreshTaskStatuses();
        $this->flashSuccess('Status de etapa atualizado com sucesso.');
    }

    public function setTaskStepStatusDefault(int $id): void
    {
        if (! $this->canManageEnvironmentSettings()) {
            $this->flashError('Apenas o proprietário pode gerenciar os status deste ambiente.');

            return;
        }

        $status = $this->taskStepStatusService->find($id, $this->taskHubInternalId);

        if ($this->isTaskStepTerminalStatus($status->title)) {
            $this->flashError('Status de conclusão e cancelamento não podem ser padrão.');

            return;
        }

        $this->taskStepStatusService->setDefaultForHub($this->taskHubInternalId, $status->id);

        $this->refreshTaskStatuses();
        $this->flashSuccess('Status padrão de etapa atualizado com sucesso.');
    }

    public function openAsideTask(int $id): void
    {
        $this->selectedStepId = null;
        $this->selectedTaskId = $id;
    }

    public function openAsideTaskStep(int $id): void
    {
        $this->selectedTaskId = null;
        $this->selectedStepId = TaskStep::query()
            ->where('task_hub_id', $this->taskHubInternalId)
            ->findOrFail($id)
            ->id;
    }

    public function storeTaskStep(int $taskId): void
    {
        $accessUserIds = $this->taskService
            ->accessUsersByHubId($this->taskHubInternalId)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $accessOrganizationIds = $this->taskService
            ->organizationAccessesByHubId($this->taskHubInternalId)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $allowedUserIds = $accessUserIds;
        $selectedOrganizationId = $this->organization_id ? (int) $this->organization_id : null;

        if ($selectedOrganizationId !== null) {
            $organization = $this->taskService
                ->organizationAccessesByHubId($this->taskHubInternalId)
                ->firstWhere('id', $selectedOrganizationId);

            if ($organization) {
                $allowedUserIds = $organization->users
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->all();
            }
        }

        $data = $this->validate([
            'step_title' => TaskStepRules::store()['title'],
            'step_user_id' => ['nullable', Rule::in($allowedUserIds)],
            'organization_id' => ['nullable', Rule::in($accessOrganizationIds)],
            'step_task_priority_id' => TaskStepRules::store()['task_priority_id'],
            'task_step_status_id' => TaskStepRules::store()['task_step_status_id'],
        ]);

        $step = $this->taskService->createStepForTask($this->taskHubInternalId, $taskId, $data);

        if ($step->organization && $step->organization->users->isNotEmpty()) {
                $this->notifyUsers(
                $step->organization->users,
                'Seu setor foi associado a uma tarefa',
                'A etapa '.$step->code.' da tarefa '.($step->task?->code ?? 'sem codigo').' foi direcionada ao setor '.$step->organization->title.'.',
                [
                    'url' => route('tasks.show', $step->task?->taskHub?->uuid ?? $this->taskHubId),
                    'icon' => 'fa-solid fa-sitemap',
                    'level' => 'info',
                ]
            );
        }

        $this->cancelCreateTaskStep();
        $this->dispatch('step-form-closed');
        $this->flashSuccess('Etapa criada com sucesso.');
    }

    #[On('task-aside-close')]
    public function closedAsideTask(): void
    {
        $this->selectedTaskId = null;
    }

    #[On('task-step-aside-close')]
    public function closedAsideTaskStep(): void
    {
        $this->selectedStepId = null;
    }

    public function openMemberShareModal(): void
    {
        $this->reset('member_user_id');
        $this->openModal('modal-task-member-share');
    }

    public function openOrganizationShareModal(): void
    {
        $this->reset('member_organization_id');
        $this->openModal('modal-task-organization-share');
    }

    public function addMember(): void
    {
        $data = $this->validate([
            'member_user_id' => 'required|exists:users,id',
        ]);

        $accessUserIds = $this->taskService
            ->accessUsers($this->taskHubId)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if (in_array((int) $data['member_user_id'], $accessUserIds, true)) {
            $this->flashError('Este usuário já possui acesso ao ambiente.');

            return;
        }

        $added = $this->taskService->addMember(
            $this->taskHubId,
            (int) Auth::id(),
            (int) $data['member_user_id']
        );

        if (! $added) {
            $this->flashError('Apenas o proprietário pode gerenciar os membros do ambiente.');

            return;
        }

        $this->member_user_id = null;
        $this->closeModal();
        $this->flashSuccess('Membro adicionado ao ambiente com sucesso.');
    }

    public function addMembersByOrganization(): void
    {
        $data = $this->validate([
            'member_organization_id' => 'required|exists:organization_charts,id',
        ]);

        $addedCount = $this->taskService->addMembersByOrganization(
            $this->taskHubId,
            (int) Auth::id(),
            (int) $data['member_organization_id']
        );

        if ($addedCount === 0) {
            $this->flashError('Nenhum usuário novo foi adicionado para este setor.');

            return;
        }

        $this->member_organization_id = null;
        $this->flashSuccess($addedCount.' usuário(s) adicionados ao ambiente.');
    }

    public function addOrganizationAccess(): void
    {
        $data = $this->validate([
            'member_organization_id' => 'required|exists:organization_charts,id',
        ]);

        $added = $this->taskService->addOrganizationAccess(
            $this->taskHubId,
            (int) Auth::id(),
            (int) $data['member_organization_id']
        );

        if (! $added) {
            $this->flashError('Não foi possível compartilhar este setor.');

            return;
        }

        $this->member_organization_id = null;
        $this->closeModal();
        $this->flashSuccess('Setor adicionado ao compartilhamento com sucesso.');
    }

    public function removeOrganizationAccess(int $organizationId): void
    {
        $removed = $this->taskService->removeOrganizationAccess(
            $this->taskHubId,
            (int) Auth::id(),
            $organizationId
        );

        if (! $removed) {
            $this->flashError('Não foi possível remover este setor do compartilhamento.');

            return;
        }

        $this->flashSuccess('Setor removido do compartilhamento com sucesso.');
    }

    public function toggleOrganizationUsers(int $organizationId): void
    {
        if (in_array($organizationId, $this->expandedOrganizationIds, true)) {
            $this->expandedOrganizationIds = array_values(
                array_filter(
                    $this->expandedOrganizationIds,
                    fn (int $id): bool => $id !== $organizationId
                )
            );

            return;
        }

        $this->expandedOrganizationIds[] = $organizationId;
    }

    public function removeMember(int $membershipId): void
    {
        $removed = $this->taskService->removeMember(
            $this->taskHubId,
            (int) Auth::id(),
            $membershipId
        );

        if (! $removed) {
            $this->flashError('Não foi possível remover este membro do ambiente.');

            return;
        }

        $this->flashSuccess('Membro removido do ambiente com sucesso.');
    }

    public function requestStepKanbanDrop(int $stepId, int $fromStatusId, int $toStatusId, array $targetOrder): void
    {
        if ($this->isInvalidStepTerminalSwap($fromStatusId, $toStatusId)) {
            $this->flashError('Não é permitido mover uma etapa cancelada para concluída ou uma etapa concluída para cancelada.');

            return;
        }

        $reasonType = $this->stepReasonTypeForTransition($fromStatusId, $toStatusId);

        if (
            $fromStatusId !== $toStatusId
            && $reasonType !== null
        ) {
            $this->pendingStepMoveStepId = $stepId;
            $this->pendingStepMoveFromStatusId = $fromStatusId;
            $this->pendingStepMoveToStatusId = $toStatusId;
            $this->pendingStepMoveTargetOrder = array_map(fn ($id): int => (int) $id, $targetOrder);
            $this->stepCompletionComment = '';
            $this->pendingStepMoveReasonType = $reasonType;
            $this->openModal('modal-step-completion-move');

            return;
        }

        $this->performStepKanbanDrop($stepId, $fromStatusId, $toStatusId, $targetOrder);
    }

    public function reorderStepKanbanCard(
        int $stepId,
        int $fromStatusId,
        int $toStatusId,
        array $targetOrder,
        ?string $completionComment = null
    ): void {
        $completionComment = $completionComment !== null ? trim($completionComment) : null;

        if ($this->isInvalidStepTerminalSwap($fromStatusId, $toStatusId)) {
            $this->flashError('Não é permitido mover uma etapa cancelada para concluída ou uma etapa concluída para cancelada.');

            return;
        }

        $reasonType = $this->stepReasonTypeForTransition($fromStatusId, $toStatusId);

        if (
            $fromStatusId !== $toStatusId
            && $reasonType !== null
            && ($completionComment === null || $completionComment === '')
        ) {
            $this->flashError(
                match ($reasonType) {
                    'completion' => 'Informe um comentário para concluir a etapa.',
                    'cancellation' => 'Informe o motivo para cancelar a etapa.',
                    'reopen' => 'Informe o motivo para reabrir a etapa.',
                    default => 'Informe um motivo para continuar.',
                }
            );

            return;
        }

        $columns = collect($this->taskService->stepKanban($this->taskHubId));
        $fullColumns = collect($this->taskService->stepKanban($this->taskHubId, []));

        $sourceColumn = $fullColumns->firstWhere('status_id', $fromStatusId);
        $targetColumn = $fullColumns->firstWhere('status_id', $toStatusId);

        if (! $sourceColumn || ! $targetColumn) {
            return;
        }

        $sourceOrder = $sourceColumn['steps']
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === $stepId)
            ->values()
            ->all();

        $visibleTargetIds = ($columns->firstWhere('status_id', $toStatusId)['steps'] ?? collect())
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === $stepId)
            ->values()
            ->all();

        $targetIds = $targetColumn['steps']
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === $stepId)
            ->values()
            ->all();

        $allowedIds = [...$targetIds, $stepId];

        $normalizedTargetOrder = collect($targetOrder === [] ? $visibleTargetIds : $targetOrder)
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => in_array($id, $allowedIds, true))
            ->unique()
            ->values();

        foreach ($allowedIds as $allowedId) {
            if (! $normalizedTargetOrder->contains($allowedId)) {
                $normalizedTargetOrder->push($allowedId);
            }
        }

        $targetOrder = $normalizedTargetOrder->all();

        $moved = $this->taskService->moveKanbanStep(
            $this->taskHubId,
            $stepId,
            $fromStatusId,
            $toStatusId,
            $sourceOrder,
            $targetOrder,
            $completionComment,
            $completionComment !== null && $fromStatusId !== $toStatusId ? $reasonType : null
        );

        if (! $moved && $fromStatusId !== $toStatusId) {
            $this->flashError('Não é possível mover esta etapa enquanto a etapa anterior obrigatória do fluxo não estiver concluída.');
        }
    }

    public function confirmStepCompletionMove(): void
    {
        $this->validate([
            'stepCompletionComment' => 'required|string|max:2000',
        ]);

        if (
            $this->pendingStepMoveStepId === null
            || $this->pendingStepMoveFromStatusId === null
            || $this->pendingStepMoveToStatusId === null
        ) {
            $this->closeModal();

            return;
        }

        $this->performStepKanbanDrop(
            $this->pendingStepMoveStepId,
            $this->pendingStepMoveFromStatusId,
            $this->pendingStepMoveToStatusId,
            $this->pendingStepMoveTargetOrder,
            $this->stepCompletionComment
        );

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->modalKey = null;
        $this->showModal = false;
        $this->resetValidation();
        $this->resetPendingStepMove();
    }

    public function silentRefresh(): void
    {
        // Polling hook: keeps the component state and refreshes render data.
    }
    /**
     * @return array<int>
     */
    private function stepInProgressStatusIds(): array
    {
        return $this->taskStepStatuses
            ->filter(function ($status): bool {
                $normalized = (string) Str::of((string) $status->title)->lower()->ascii();

                return str_contains($normalized, 'andamento') || str_contains($normalized, 'execu');
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function stepReasonTypeForTransition(int $fromStatusId, int $toStatusId): ?string
    {
        return $this->taskService->stepReasonTypeForTransition(
            $this->taskHubInternalId,
            $fromStatusId === 0 ? null : $fromStatusId,
            $toStatusId === 0 ? null : $toStatusId
        );
    }

    private function isInvalidStepTerminalSwap(int $fromStatusId, int $toStatusId): bool
    {
        return $this->taskService->isInvalidStepTerminalSwap(
            $this->taskHubInternalId,
            $fromStatusId === 0 ? null : $fromStatusId,
            $toStatusId === 0 ? null : $toStatusId
        );
    }

    /**
     * @return array<int>
     */
    private function stepCompletionStatusIds(): array
    {
        return $this->taskStepStatuses
            ->filter(function ($status): bool {
                $normalized = (string) Str::of((string) $status->title)->lower()->ascii();

                return str_contains($normalized, 'conclu') || str_contains($normalized, 'finaliz');
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    private function performStepKanbanDrop(
        int $stepId,
        int $fromStatusId,
        int $toStatusId,
        array $targetOrder,
        ?string $completionComment = null
    ): void {
        $this->reorderStepKanbanCard($stepId, $fromStatusId, $toStatusId, $targetOrder, $completionComment);
    }

    private function stepKanbanHasActiveFilters(): bool
    {
        return collect($this->stepKanbanFilters)
            ->contains(fn ($value): bool => $value !== null && $value !== '');
    }

    private function resetPendingStepMove(): void
    {
        $this->pendingStepMoveStepId = null;
        $this->pendingStepMoveFromStatusId = null;
        $this->pendingStepMoveToStatusId = null;
        $this->pendingStepMoveTargetOrder = [];
        $this->stepCompletionComment = '';
        $this->pendingStepMoveReasonType = null;
    }

    private function canManageEnvironmentSettings(): bool
    {
        return $this->taskHubOwnerId === (int) Auth::id();
    }

    /**
     * @return array<string, string>
     */
    private function statusPaletteOptions(): array
    {
        return [
            'gray' => 'Cinza',
            'blue' => 'Azul',
            'green' => 'Verde',
            'yellow' => 'Amarelo',
            'red' => 'Vermelho',
        ];
    }

    /**
     * @return array{0:string, 1:string}
     */
    private function paletteToStatusStyle(string $palette): array
    {
        return match ($palette) {
            'blue' => ['blue', 'bg-blue-100 text-blue-700 hover:bg-blue-200'],
            'green' => ['green', 'bg-green-100 text-green-700 hover:bg-green-200'],
            'yellow' => ['yellow', 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'],
            'red' => ['red', 'bg-red-100 text-red-700 hover:bg-red-200'],
            default => ['gray', 'bg-gray-100 text-gray-700 hover:bg-gray-200'],
        };
    }

    private function isTaskTerminalStatus(string $title): bool
    {
        $normalized = (string) Str::of($title)->lower()->ascii();

        return str_contains($normalized, 'conclu') || str_contains($normalized, 'cancel');
    }

    private function isTaskStepTerminalStatus(string $title): bool
    {
        $normalized = (string) Str::of($title)->lower()->ascii();

        return str_contains($normalized, 'conclu') || str_contains($normalized, 'cancel');
    }

    public function render()
    {
        $members = $this->taskService->members($this->taskHubId);
        $responsibleUsers = $this->taskService->accessUsers($this->taskHubId);
        $accessEntries = $this->taskService->accessUserEntries($this->taskHubId);
        $organizationAccesses = $this->taskService->organizationAccesses($this->taskHubId);
        $memberUserIds = $members
            ->pluck('user_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $accessUserIds = $responsibleUsers
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $organizationAccessIds = $organizationAccesses
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $stepFormUsers = $responsibleUsers;

        if ($this->organization_id) {
            $organization = $organizationAccesses->firstWhere('id', (int) $this->organization_id);
            if ($organization) {
                $stepFormUsers = $organization->users
                    ->sortBy(fn (User $user) => $user->name ?? '')
                    ->values();
            }
        }

        $stepKanbanTaskOptions = Task::query()
            ->where('task_hub_id', $this->taskHubInternalId)
            ->orderBy('code')
            ->orderBy('title')
            ->get(['id', 'code', 'title']);

        return view('livewire.task.task-page', [
            'tasks' => $this->taskService->index($this->taskHubId, $this->filters),
            'dashboard' => $this->taskService->dashboard($this->taskHubId),
            'stepKanban' => $this->taskService->stepKanban($this->taskHubId, $this->stepKanbanFilters),
            'stepKanbanTaskOptions' => $stepKanbanTaskOptions,
            'stepKanbanFiltersActive' => $this->stepKanbanHasActiveFilters(),
            'stepCompletionStatusIds' => $this->stepCompletionStatusIds(),
            'members' => $members,
            'responsibleUsers' => $responsibleUsers,
            'accessEntries' => $accessEntries,
            'organizationAccesses' => $organizationAccesses,
            'accessibleOrganizations' => $organizationAccesses,
            'stepFormUsers' => $stepFormUsers,
            'canManageSettings' => $this->canManageEnvironmentSettings(),
            'canManageMembers' => $this->taskHubOwnerId === (int) Auth::id(),
            'availableMemberUsers' => $this->users
                ->reject(fn (User $user): bool => in_array((int) $user->id, $memberUserIds, true))
                ->reject(fn (User $user): bool => in_array((int) $user->id, $accessUserIds, true))
                ->values(),
            'availableOrganizations' => $this->organizations
                ->reject(fn (OrganizationChart $organization): bool => in_array((int) $organization->id, $organizationAccessIds, true))
                ->values(),
        ]);
    }
}


