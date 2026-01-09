<?php

namespace App\Livewire\Admin\Manage\Establishment;

use App\Models\Manage\Company\Department;
use Livewire\Component;
use Livewire\WithPagination;

use function Pest\Laravel\session;

class DepartmentTable extends Component
{
    use WithPagination;

    public $establishmentId;
    public $search = '';
    public $title;
    public $acronym;
    public $successCreate = null;

    protected $updatesQueryString = ['search'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'acronym' => 'required|string|min:3|max:20',
    ];

    public function store()
    {
        $this->validate();

        Department::create([
            'title' => $this->title,
            'acronym' => $this->acronym,
            'establishment_id' => $this->establishmentId,
        ]);
        
        $this->reset(['title', 'acronym']);

        $this->successCreate = 'Cadastro de departamento realizado com sucesso!';
    }

    public function mount($establishmentId)
    {
        $this->establishmentId = $establishmentId;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $departments = Department::where('establishment_id', $this->establishmentId)
            ->when($this->search, fn ($q) =>
                $q->where('filter', 'like', '%' . str()->lower($this->search) . '%')
            )
            ->orderBy('title')
            ->paginate(5);

        return view('livewire.admin.manage.establishment.department-table', compact('departments'));
    }
}
