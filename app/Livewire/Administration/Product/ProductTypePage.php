<?php

namespace App\Livewire\Administration\Product;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Administration\Product\ProductTypeService;
use App\Validation\Administration\Product\ProductTypeRules;
use Illuminate\View\View;
use Livewire\Component;

class ProductTypePage extends Component
{
    use Modal, WithFlashMessage;

    protected ProductTypeService $productTypeService;

    public ?int $productTypeId = null;
    public string $title = '';
    public ?string $description = null;

    public function boot(ProductTypeService $productTypeService): void
    {
        $this->productTypeService = $productTypeService;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->openModal('modal-form-create-product-type');
    }

    public function store(): void
    {
        $data = $this->validate(ProductTypeRules::store());
        $this->productTypeService->create($data);

        $this->flashSuccess('Tipo de produto cadastrado com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $productType = $this->productTypeService->find($id);

        $this->productTypeId = $productType->id;
        $this->title = $productType->title;
        $this->description = $productType->description;

        $this->openModal('modal-form-edit-product-type');
    }

    public function update(): void
    {
        if (! $this->productTypeId) {
            return;
        }

        $data = $this->validate(ProductTypeRules::update($this->productTypeId));
        $this->productTypeService->update($this->productTypeId, $data);

        $this->flashSuccess('Tipo de produto atualizado com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.administration.product.product-type-page', [
            'productTypes' => $this->productTypeService->index(),
        ])->layout('layouts.app');
    }

    private function resetForm(): void
    {
        $this->reset([
            'productTypeId',
            'title',
            'description',
        ]);
    }
}

