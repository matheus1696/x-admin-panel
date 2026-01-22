<?php

namespace App\Livewire\Traits;

trait Modal
{
    public bool $showModal = false;
    public ?string $modalKey = null;  // identifica o tipo de modal

    /**
     * Abre o modal
     *
     * @param string $key Identifica o modal, ex: 'ativar_atividade', 'formulario', 'desativar_atividade'
     * @param mixed|null $data Dados específicos da ação
     */
    public function openModal(string $key, $data = null)
    {
        $this->modalKey = $key;
        $this->showModal = true;
        $this->resetValidation();
    }

    /**
     * Fecha o modal
     */
    public function closeModal()
    {
        $this->modalKey = null;
        $this->showModal = false;
        $this->resetValidation();
    }
}
