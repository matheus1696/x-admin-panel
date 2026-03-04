<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Services\Notification\NotificationService;
use App\Services\Task\TaskService;

class DashboardController extends Controller
{
    public function index(TaskService $taskService, NotificationService $notificationService)
    {
        ActivityLogHelper::action('Pagina do painel apos autenticacao');
        $user = request()->user();

        return view('dashboard', [
            'taskOverview' => $taskService->userOverview((int) $user->id),
            'notificationSummary' => $notificationService->summaryForUser($user),
        ]);
    }
}
