<?php

namespace App\Livewire\Admin\Manage\Establishment;

use App\Models\Manage\Company\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentTable extends Component
{
    use WithPagination;

    public $establishmentId;
    public $search = '';

    protected $updatesQueryString = ['search'];

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
            ->paginate(10);

        return view('livewire.admin.manage.establishment.department-table', compact('departments'));
    }
}
