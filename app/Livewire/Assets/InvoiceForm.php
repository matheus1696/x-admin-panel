<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\CreateInvoiceDTO;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\User\User;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetInvoice;
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
    public string $supplierName = '';
    public ?string $supplierDocument = null;
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
            $this->issueDate = now()->toDateString();

            return;
        }

        $invoice = AssetInvoice::query()->where('uuid', $uuid)->firstOrFail();

        $this->invoiceId = $invoice->id;
        $this->invoiceUuid = $invoice->uuid;
        $this->invoiceNumber = $invoice->invoice_number;
        $this->invoiceSeries = $invoice->invoice_series;
        $this->supplierName = $invoice->supplier_name;
        $this->supplierDocument = $invoice->supplier_document;
        $this->issueDate = optional($invoice->issue_date)->toDateString() ?? '';
        $this->receivedDate = optional($invoice->received_date)->toDateString();
        $this->totalAmount = number_format((float) $invoice->total_amount, 2, '.', '');
        $this->notes = $invoice->notes;
    }

    public function save()
    {
        $data = $this->validate([
            'invoiceNumber' => ['required', 'string', 'max:255'],
            'invoiceSeries' => ['nullable', 'string', 'max:255'],
            'supplierName' => ['required', 'string', 'max:255'],
            'supplierDocument' => ['nullable', 'string', 'max:255'],
            'issueDate' => ['required', 'date'],
            'receivedDate' => ['nullable', 'date'],
            'totalAmount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $dto = new CreateInvoiceDTO(
            invoiceNumber: $data['invoiceNumber'],
            invoiceSeries: $data['invoiceSeries'],
            supplierName: $data['supplierName'],
            supplierDocument: $data['supplierDocument'],
            issueDate: $data['issueDate'],
            receivedDate: $data['receivedDate'],
            totalAmount: $data['totalAmount'],
            notes: $data['notes'],
            createdUserId: $this->invoiceId ? null : User::find(auth()->id())?->id,
        );

        $invoice = $this->invoiceId
            ? $this->invoiceService->updateInvoice($this->invoiceId, $dto)
            : $this->invoiceService->createInvoice($dto);

        $this->flashSuccess($this->invoiceId
            ? __('assets.invoices.messages.updated')
            : __('assets.invoices.messages.created'));

        return redirect()->route('assets.invoices.show', $invoice->uuid);
    }

    public function render(): View
    {
        return view('livewire.assets.invoice-form')->layout('layouts.app');
    }
}
