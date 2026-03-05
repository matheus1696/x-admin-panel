<?php

namespace App\Livewire\Administration\Product;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Administration\Product\ProductMeasureUnitService;
use App\Validation\Administration\Product\ProductMeasureUnitRules;
use Illuminate\View\View;
use Livewire\Component;

class ProductMeasureUnitPage extends Component
{
    use Modal, WithFlashMessage;

    protected ProductMeasureUnitService $productMeasureUnitService;

    public ?int $measureUnitId = null;
    public string $acronym = '';
    public string $title = '';
    public int $base_quantity = 1;

    public function boot(ProductMeasureUnitService $productMeasureUnitService): void
    {
        $this->productMeasureUnitService = $productMeasureUnitService;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->openModal('modal-form-create-product-measure-unit');
    }

    public function store(): void
    {
        $data = $this->validate(ProductMeasureUnitRules::store());
        $this->productMeasureUnitService->create($data);

        $this->flashSuccess('Unidade de medida cadastrada com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $measureUnit = $this->productMeasureUnitService->find($id);

        $this->measureUnitId = $measureUnit->id;
        $this->acronym = $measureUnit->acronym;
        $this->title = $measureUnit->title;
        $this->base_quantity = (int) $measureUnit->base_quantity;

        $this->openModal('modal-form-edit-product-measure-unit');
    }

    public function update(): void
    {
        if (! $this->measureUnitId) {
            return;
        }

        $data = $this->validate(ProductMeasureUnitRules::update($this->measureUnitId));
        $this->productMeasureUnitService->update($this->measureUnitId, $data);

        $this->flashSuccess('Unidade de medida atualizada com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.administration.product.product-measure-unit-page', [
            'measureUnits' => $this->productMeasureUnitService->index(),
        ])->layout('layouts.app');
    }

    private function resetForm(): void
    {
        $this->reset([
            'measureUnitId',
            'acronym',
            'title',
        ]);

        $this->base_quantity = 1;
    }
}

