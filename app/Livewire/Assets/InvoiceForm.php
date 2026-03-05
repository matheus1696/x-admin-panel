<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\CreateInvoiceDTO;
use App\Livewire\Traits\WithFlashMessage;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Administration\Supplier\Supplier;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Services\Assets\InvoiceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceForm extends Component
{
    use WithFlashMessage;

    protected InvoiceService $invoiceService;

    public ?int $invoiceId = null;
    public ?string $invoiceUuid = null;
    public string $invoiceNumber = '';
    public ?string $invoiceSeries = null;
    public ?int $financialBlockId = null;
    public ?int $supplierId = null;
    public ?string $supplyOrder = null;
    public string $issueDate = '';
    public ?string $receivedDate = null;
    public string $totalAmount = '0.00';
    public ?string $notes = null;

    public function boot(InvoiceService $invoiceService): void
    {
        $this->invoiceService = $invoiceService;
    }

    public function mount(?string $uuid = null): void
    {
        Gate::authorize('manageInvoices', Asset::class);

        if ($uuid === null) {
            $this->issueDate = '';
            $this->receivedDate = now()->toDateString();
            $this->totalAmount = '0.00';

            return;
        }

        $invoice = AssetInvoice::query()->where('uuid', $uuid)->firstOrFail();

        $this->invoiceId = $invoice->id;
        $this->invoiceUuid = $invoice->uuid;
        $this->invoiceNumber = $invoice->invoice_number;
        $this->invoiceSeries = $invoice->invoice_series;
        $this->financialBlockId = $invoice->financial_block_id;
        $this->supplierId = Supplier::query()
            ->when($invoice->supplier_document, fn ($query) => $query->where('document', $invoice->supplier_document))
            ->where('title', $invoice->supplier_name)
            ->value('id');
        $this->supplyOrder = $invoice->supply_order;
        $this->issueDate = optional($invoice->issue_date)->toDateString() ?? '';
        $this->receivedDate = optional($invoice->received_date)->toDateString();
        $this->totalAmount = number_format((float) $invoice->total_amount, 2, '.', '');
        $this->notes = $invoice->notes;
    }

    public function save()
    {
        if ($this->invoiceId && AssetInvoice::query()->findOrFail($this->invoiceId)->is_finalized) {
            $this->flashWarning('Esta nota fiscal ja foi finalizada e nao pode mais ser editada.');

            return redirect()->route('assets.invoices.show', $this->invoiceUuid);
        }

        $data = $this->validate([
            'invoiceNumber' => ['required', 'string', 'max:255'],
            'invoiceSeries' => ['nullable', 'string', 'max:255'],
            'financialBlockId' => ['required', 'integer', 'exists:financial_blocks,id'],
            'supplierId' => ['required', 'integer', 'exists:suppliers,id'],
            'supplyOrder' => ['nullable', 'regex:/^\d{4,5}-\d{4}$/'],
            'issueDate' => ['required', 'date', 'before_or_equal:today'],
            'receivedDate' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ]);

        $supplier = Supplier::query()->findOrFail((int) $data['supplierId']);
        $currentTotal = $this->invoiceId
            ? (float) AssetInvoice::query()->findOrFail($this->invoiceId)->total_amount
            : 0;

        $dto = new CreateInvoiceDTO(
            invoiceNumber: $data['invoiceNumber'],
            invoiceSeries: $data['invoiceSeries'],
            financialBlockId: (int) $data['financialBlockId'],
            supplierName: $supplier->title,
            supplierDocument: $supplier->document,
            supplyOrder: $data['supplyOrder'],
            issueDate: $data['issueDate'],
            receivedDate: $data['receivedDate'],
            totalAmount: $currentTotal,
            notes: $data['notes'],
            createdUserId: $this->invoiceId ? null : User::find(auth()->id())?->id,
        );

        try {
            $invoice = $this->invoiceId
                ? $this->invoiceService->updateInvoice($this->invoiceId, $dto)
                : $this->invoiceService->createInvoice($dto);
        } catch (AssetsValidationException $exception) {
            $this->flashWarning($exception->getMessage());

            return null;
        }

        $this->flashSuccess($this->invoiceId
            ? 'Nota fiscal atualizada com sucesso.'
            : 'Nota fiscal cadastrada com sucesso.');

        return redirect()->route('assets.invoices.show', $invoice->uuid);
    }

    public function render(): View
    {
        return view('livewire.assets.invoice-form', [
            'suppliers' => Supplier::query()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(),
            'financialBlocks' => FinancialBlock::query()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(),
        ])->layout('layouts.app');
    }
}
