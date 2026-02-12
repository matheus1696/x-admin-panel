<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepCategory;
use App\Models\Administration\User\User;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Task\Task;
use App\Models\Task\TaskStep;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskRules;
use App\Validation\Task\TaskStepRules;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TaskPage extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected TaskService $taskService;
    protected TaskStatusService $taskStatusService;
    protected TaskStepStatusService $taskStepStatusesService;

    public array $filters = [
        'title' => '',
        'workflow_run_status_id' => 'all',
        'perPage' => 50,
    ];

    public Collection $users;
    public Collection $taskCategories;
    public Collection $taskPriorities;
    public Collection $taskStatuses;
    public Collection $taskStepCategories;
    public Collection $taskStepStatuses;
    public Collection $workflows;

    public $taskId;

    public ?string $title = null;
    public ?int $user_id = null;
    public ?int $task_category_id = null;
    public ?int $task_priority_id = null;
    public ?int $task_status_id = null;
    public ?int $task_step_status_id = null;
    public ?int $workflow_id = null;

    public bool $isCreatingTask = false;
    public bool $isCreatingTaskStep = false;
    public ?int $selectedTaskId = null;
    public ?int $selectedTaskStepId = null;

    public function boot( TaskService $taskService,  TaskStatusService $taskStatusService, TaskStepStatusService $taskStepStatusesService)
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusesService = $taskStepStatusesService;
    }

    protected function setDefaults(): void
    {
        $this->task_priority_id = $this->taskPriorities->firstWhere('is_default', true)?->id;
        $this->task_status_id = collect($this->taskStatuses)->firstWhere('is_default', true)?->id;
        $this->task_step_status_id = collect($this->taskStepStatuses)->firstWhere('is_default', true)?->id;
    }

    public function mount()
    {
        $this->users = User::orderBy('name')->get();
        $this->taskCategories = TaskCategory::orderBy('title')->get();
        $this->taskPriorities = TaskPriority::orderBy('level')->get();
        $this->taskStatuses = $this->taskStatusService->index();
        $this->taskStepCategories = TaskStepCategory::orderBy('title')->get();
        $this->taskStepStatuses = $this->taskStepStatusesService->index();
        $this->workflows = Workflow::orderBy('title')->get();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset('title', 'user_id', 'task_category_id', 'task_priority_id', 'task_status_id');
    }

    // Início Formulário de Criação de Tarefa
        public function enableCreateTask()
        {
            $this->resetForm();
            $this->setDefaults();
            $this->isCreatingTask = true;
        }
        
        public function cancelCreateTask()
        {
            $this->resetForm();
            $this->setDefaults();
            $this->isCreatingTask = false;
        }

        public function storeTask()
        {
            $data = $this->validate(TaskRules::store());

            $this->taskService->create($data);

            $this->isCreatingTask = false;
            $this->resetForm();
            $this->flashSuccess('Tarefa criada com sucesso.');
        }
    // Fim

    // Início Formulário de Criação de Etapas
        public function enableCreateTaskStep($id)
        {
            $this->resetForm();
            $this->setDefaults();
            $this->taskId = $id;
            $this->isCreatingTaskStep = true;
        }
        
        public function cancelCreateTaskStep()
        {
            $this->resetForm();
            $this->setDefaults();
            $this->taskId = null;
            $this->isCreatingTaskStep = false;
        }
        
        public function storeStep($id)
        {
            $data = $this->validate(TaskStepRules::store());
            $data['task_id'] = $id;
            $data['task_status_id'] = $data['task_step_status_id'];

            TaskStep::create($data);

            $this->isCreatingTaskStep = false;
            $this->resetForm();
            $this->flashSuccess('Tarefa criada com sucesso.');
        }
    // Fim

    // Início Formulário de Copia do Fluxo de Trabalho
        public function openCopyWorkflowModal(int $taskId)
        {
            $this->reset(['workflow_id']);
            $this->taskId = $taskId;

            $this->openModal('modal-copy-workflow-steps');
        }

        public function copyWorkflowSteps()
        {
            if (TaskStep::where('task_id', $this->taskId)->exists()) {
                $this->flashError('Esta tarefa já possui etapas.');
                return;
            }

            $workflow = Workflow::findOrFail($this->workflow_id);
            $currentDeadline = now();
            $this->setDefaults();

            foreach ($workflow->workflowSteps as $key => $step) {

                if ($step->deadline_days) {
                    $currentDeadline = $currentDeadline->copy()->addDays($step->deadline_days);
                }

                $taskStep = TaskStep::create([
                    'task_id'     => $this->taskId,
                    'title'       => $step->title,
                    'organization_id' => $step->organization_id,
                    'deadline_at' => $step->deadline_days ? $currentDeadline : null,
                    'task_status_id'   => $this->task_step_status_id,
                    'task_priority_id'   => $this->task_priority_id,
                ]);
            }

            $taskUpdate = Task::find($this->taskId);
            $taskUpdate->deadline_at = $taskStep->deadline_at;
            $taskUpdate->save();

            $this->flashSuccess('Etapas copiadas com sucesso.');
            $this->closeModal();
        }
    // Fim

    // Início Informações Aside das Tarefas
        public function openAsideTask($id)
        {
            $this->selectedTaskId = $id;
        }
        
        public function openAsideTaskStep($id)
        {
            $this->selectedTaskStepId = $id;
        }
        
        public function closedAsideTask()
        {
            $this->resetPage();
            $this->selectedTaskId = null;
            $this->selectedTaskStepId = null;
        }
    //Fim

    public function render()
    { 
        return view('livewire.task.task-page',[
            'tasks' => $this->taskService->index($this->filters),
        ]);
    }
}
