<?php

namespace App\Livewire\Configuration\Financial\FinancialBlock;

use App\Models\Manage\Company\FinancialBlock;
use Livewire\Component;
use Livewire\WithPagination;

class FinancialBlockPage extends Component
{
    use WithPagination;

    public $acronym = '';
    public $name = '';
    public $status = 'all';
    public $sort = 'name_asc';
    public $perPage = 10;

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = FinancialBlock::query();

        $query->orderBy('status', 'desc');

        if ($this->acronym) { $query->where('acronym', 'like', '%' . strtolower($this->acronym) . '%'); }

        if ($this->name) { $query->where('filter', 'like', '%' . strtolower($this->name) . '%'); }

        if ($this->status !== 'all') { $query->where('status', $this->status); }

        // Ordenação
        switch ($this->sort) {
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
        }

        $financialBlocks = $query->paginate($this->perPage);

        return view('livewire.configuration.financial.financial-block.financial-block-page',[
            'financialBlocks' => $financialBlocks,
        ])->layout('layouts.app');
    }
}
