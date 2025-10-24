<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogTable extends Component
{
    use WithPagination;

    // Filtros
    public $dateStart;
    public $dateEnd;
    public $ip;
    public $user;
    public $url;
    public $perPage = 100;

    public function mount()
    {
        $this->dateStart = Carbon::today()->subDays(7)->format('Y-m-d');
        $this->dateEnd = Carbon::today()->format('Y-m-d');
    }

    public function render()
    {
        $query = ActivityLog::query()
            ->with('User')
            ->latest('created_at');

        // Filtro por data inicial
        if ($this->dateStart) {
            $query->whereDate('created_at', '>=', $this->dateStart);
        }

        // Filtro por data final
        if ($this->dateEnd) {
            $query->whereDate('created_at', '<=', $this->dateEnd);
        }

        // Filtro por IP
        if ($this->ip) {
            $query->where('ip_address', 'like', '%' . $this->ip . '%');
        }

        // Filtro por Usuário
        if ($this->user) {
            $query->whereHas('User', function ($q) {
                $q->where('uuid', $this->user);
            });
        }

        // Filtro por URL
        if ($this->url) {
            $query->where('url', 'like', '%' . $this->url . '%');
        }

        // Paginação
        $logs = $query->paginate($this->perPage);

        //User
        $users = User::orderBy('name')->get();

        return view('livewire.activity-log-table', compact('logs', 'users'));
    }
}
