<?php

namespace App\Livewire\Traits;

trait Modal
{
    public bool $showModal = false;
    public string $mode = 'create';

    /**
     * Abre o modal em modo 'create'
     */
    public function createModal()
    {
        $this->resetForm();
        $this->mode = 'create';
        $this->showModal = true;
    }

    /**
     * Abre o modal em modo 'edit', passando o ID
     */
    public function editModal(int $id)
    {
        $this->resetForm();
        $this->loadModel($id);
        $this->mode = 'edit';
        $this->showModal = true;
    }

    /**
     * Fecha o modal e reseta formulário
     */
    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }
}
