<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Services\Notification\NotificationService;
use App\Services\Process\ProcessService;
use App\Services\Task\TaskService;

class DashboardController extends Controller
{
    public function index(
        TaskService $taskService,
        NotificationService $notificationService,
        ProcessService $processService
    ) {
        ActivityLogHelper::action('Pagina do painel apos autenticacao');
        $user = request()->user();

        return view('dashboard', [
            'processEntries' => $user->can('process.view')
                ? $processService->dashboardEntries((int) $user->id)
                : collect(),
            'processStatuses' => $processService->availableStatuses(),
            'taskOverview' => $taskService->userOverview((int) $user->id),
            'notificationSummary' => $notificationService->summaryForUser($user),
        ]);
    }
}
