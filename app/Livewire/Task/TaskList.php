<?php

namespace App\Livewire\Task;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Task\TaskCategory;
use App\Models\Administration\Task\TaskPriority;
use App\Models\Administration\Task\TaskStepStatus;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Models\Organization\Workflow\Workflow;
use App\Models\Task\Task;
use App\Models\Task\TaskStep;
use App\Services\Administration\Task\TaskStatusService;
use App\Services\Administration\Task\TaskStepStatusService;
use App\Services\Task\TaskService;
use App\Validation\Task\TaskStepRules;
use Livewire\Component;

class TaskList extends Component
{
    use Modal, WithFlashMessage;

    protected TaskService $taskService;
    protected TaskStatusService $taskStatusService;
    protected TaskStepStatusService $taskStepStatusesService;

    public int $taskId;
    public int $workflow_id;

    public $responsable_id;

    public $title;
    public ?int $user_id = null;
    public ?int $task_category_id = null;
    public ?int $task_priority_id = null;
    public ?int $task_step_status_id = null;
    public $deadline_at;

    public function boot( TaskService $taskService, TaskStatusService $taskStatusService, TaskStepStatusService $taskStepStatusesService )
    {
        $this->taskService = $taskService;
        $this->taskStatusService = $taskStatusService;
        $this->taskStepStatusesService = $taskStepStatusesService;
    }

    protected function setDefaults(): void
    {
        $this->task_priority_id = TaskPriority::where('is_default', true)->value('id');
        $this->task_step_status_id = TaskStepStatus::where('is_default', true)->value('id');
    }

    public function mount()
    {
        $this->setDefaults();
    }

    public function resetForm()
    {
        $this->reset('title', 'user_id', 'task_category_id', 'task_priority_id', 'task_step_status_id', 'deadline_at');
    }

    public function updatedResponsableId()
    {
        $data = $this->validate(TaskStepRules::responsable());

        Task::where('id', $this->taskId)->update($data);
        
        $this->flashSuccess('Responsável atualizado.');
    }

    public function createStep()
    {
        $this->resetForm();
        $this->setDefaults();
    }
    
    public function storeStep()
    {
        $data = $this->validate(TaskStepRules::store());
        $data['task_id'] = $this->taskId;
        TaskStep::create($data);

        $this->resetForm();
        $this->flashSuccess('Tarefa criada com sucesso.');
        $this->setDefaults();
    }

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

        foreach ($workflow->workflowSteps as $key => $step) {

            if ($step->deadline_days) {
                $currentDeadline = $currentDeadline->copy()->addDays($step->deadline_days);
            }

            $taskStep = TaskStep::create([
                'task_id'     => $this->taskId,
                'title'       => $step->title,
                'organization_id' => $step->organization_id,
                'deadline_at' => $step->deadline_days ? $currentDeadline : null,
                'task_status_id'   => TaskStepStatus::default()->id,
                'task_priority_id'   => TaskPriority::default()->id,
            ]);
        }

        $taskUpdate = Task::find($this->taskId);
        $taskUpdate->deadline_at = $taskStep->deadline_at;
        $taskUpdate->save();

        $this->flashSuccess('Etapas copiadas com sucesso.');
        $this->closeModal();
    }

    public function render()
    {
        
        return view('livewire.task.task-list',[
            'task' => $this->taskService->find($this->taskId),
            'users' => User::orderBy('name')->get(),
            'organizations' => OrganizationChart::orderBy('hierarchy')->get(),
            'taskCategories' => TaskCategory::orderBy('title')->get(),
            'taskPriorities' => TaskPriority::orderBy('level')->get(),
            'taskStepStatuses' => $this->taskStepStatusesService->index(),
            'workflows' => Workflow::orderBy('title')->get(),
        ]);
    }
}
