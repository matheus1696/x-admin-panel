<?php

namespace App\Livewire\Administration\Product;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\Product\ProductDepartment;
use App\Models\Administration\Product\ProductMeasureUnit;
use App\Models\Administration\Product\ProductType;
use App\Services\Administration\Product\ProductService;
use App\Validation\Administration\Product\ProductRules;
use Illuminate\View\View;
use Livewire\Component;

class ProductPage extends Component
{
    use Modal, WithFlashMessage;

    protected ProductService $productService;

    public ?int $productId = null;
    public ?string $code = null;
    public ?string $sku = null;
    public string $title = '';
    public string $nature = 'ASSET';
    public ?int $product_department_id = null;
    public ?int $product_type_id = null;
    public ?int $default_measure_unit_id = null;
    public ?string $description = null;

    public function boot(ProductService $productService): void
    {
        $this->productService = $productService;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->applySuggestedDepartment();
        $this->openModal('modal-form-create-product');
    }

    public function store(): void
    {
        $data = $this->validate(ProductRules::store());
        $this->productService->create($data);

        $this->flashSuccess('Produto cadastrado com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $product = $this->productService->find($id);

        $this->productId = $product->id;
        $this->code = $product->code;
        $this->sku = $product->sku;
        $this->title = $product->title;
        $this->nature = (string) $product->nature;
        $this->product_department_id = $product->product_department_id;
        $this->product_type_id = $product->product_type_id;
        $this->default_measure_unit_id = $product->default_measure_unit_id;
        $this->description = $product->description;

        $this->openModal('modal-form-edit-product');
    }

    public function update(): void
    {
        if (! $this->productId) {
            return;
        }

        $data = $this->validate(ProductRules::update($this->productId));
        $this->productService->update($this->productId, $data);

        $this->flashSuccess('Produto atualizado com sucesso.');
        $this->closeModal();
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.administration.product.product-page', [
            'products' => $this->productService->index(),
            'productDepartments' => ProductDepartment::query()->orderBy('name')->get(),
            'productTypes' => ProductType::query()->orderBy('title')->get(),
            'measureUnits' => ProductMeasureUnit::query()->orderBy('title')->get(),
        ])->layout('layouts.app');
    }

    public function updatedNature(): void
    {
        $this->applySuggestedDepartment();
    }

    public function updatedProductTypeId(): void
    {
        $this->applySuggestedDepartment();
    }

    private function resetForm(): void
    {
        $this->reset([
            'productId',
            'code',
            'sku',
            'title',
            'nature',
            'product_department_id',
            'product_type_id',
            'default_measure_unit_id',
            'description',
        ]);

        $this->nature = 'ASSET';
    }

    private function applySuggestedDepartment(): void
    {
        $this->product_department_id = $this->suggestedDepartmentId();
    }

    private function suggestedDepartmentId(): ?int
    {
        $typeTitle = ProductType::query()
            ->whereKey($this->product_type_id)
            ->value('title');
        $typeTitle = mb_strtolower((string) $typeTitle);

        if (str_contains($typeTitle, 'aliment')) {
            return $this->departmentIdByCode('NUTRICAO');
        }

        if ($this->nature === 'ASSET' || str_contains($typeTitle, 'equipamento')) {
            return $this->departmentIdByCode('PATRIMONIO');
        }

        return $this->departmentIdByCode('ALMOX');
    }

    private function departmentIdByCode(string $code): ?int
    {
        return ProductDepartment::query()
            ->where('code', $code)
            ->value('id');
    }
}
