<?php

namespace App\Livewire\Company;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Company\OrganizationChart;
use Livewire\Component;

class OrganizationChartConfigPage extends Component
{
    use WithFlashMessage;
    use Modal;

    // form
    public $chartId = null;
    public $name;
    public $acronym;
    public $hierarchy;
    public $order;

    public function resetForm()
    {
        $this->reset(['chartId', 'name', 'acronym', 'hierarchy', 'order']);
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal('modal-form-create-organitation-chart');
    }

    public function edit($id)
    {
        $this->resetForm();
        
        $organizationChart = OrganizationChart::find($id);

        $this->name = $organizationChart->name;
        $this->acronym = $organizationChart->acronym;        
        
        $this->openModal('modal-form-edit-organitation-chart');
    }

    public function store()
    {
        $data = $this->validate([
            'name' => 'required|string',
            'acronym' => 'nullable|string|max:20',
            'hierarchy' => 'nullable|exists:organization_charts,id'
        ]);

        OrganizationChart::create($data);

        //Reordenando Setores
            //Buscando dados Setores
            $organizations = OrganizationChart::orderBy('hierarchy')->get();

            foreach ($organizations as $organization) {

                //Atribuindo Hierariquia do Setor Principal
                if ($organization['hierarchy'] == 0) {
                    $orderList = OrganizationChart::find($organization['id']);
                    $orderList->order = "0" . $organization['acronym'];
                    $orderList->number_hierarchy = 1;
                    $orderList->save();
                }

                //Listando Setores para Ordenação Hierarquica
                    //Buscando Dados do Predecessor (Acima do setor)
                    $predecessor = OrganizationChart::where('id', $organization['hierarchy'])->get();

                foreach ($predecessor as $valuepredecessor) {
                    //Buscando dados
                    $orderList = OrganizationChart::find($organization['id']);

                    //Atribuindo Novo Valor
                    $number_hierarchy = $valuepredecessor['order'] . $organization['id'] . $organization['acronym'];

                    //Salvando
                    $orderList->order = $number_hierarchy;
                    $orderList->number_hierarchy = preg_match_all('!\d+!',$number_hierarchy);
                    $orderList->save();
                }
            }

        $this->resetForm();
        $this->flashSuccess('Setor adicinado no organograma com sucesso.');
        $this->closeModal();
        
    }

    public function delete($id)
    {
        OrganizationChart::where('hierarchy', $id)->delete();
        OrganizationChart::findOrFail($id)->delete();
    }

    public function render()
    {
        $organizationCharts = OrganizationChart::orderBy('order')->get();

        return view('livewire.company.organization-chart-config-page',[
            'organizationCharts' => $organizationCharts,
        ])->layout('layouts.app');
    }
}
