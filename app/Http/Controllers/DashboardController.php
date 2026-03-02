<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Services\Task\TaskService;

class DashboardController extends Controller
{
    public function index(TaskService $taskService)
    {
        ActivityLogHelper::action('Página do painel após autenticação');

        return view('dashboard', [
            'taskOverview' => $taskService->userOverview((int) request()->user()->id),
        ]);
    }
}
