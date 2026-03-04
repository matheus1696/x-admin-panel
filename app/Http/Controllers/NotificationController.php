<?php

namespace App\Http\Controllers;

use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(NotificationService $notificationService): View
    {
        $user = request()->user();

        return view('notifications.index', [
            'notificationSummary' => $notificationService->summaryForUser($user),
            'notifications' => $notificationService->paginateForUser($user),
        ]);
    }

    public function read(string $notificationId, NotificationService $notificationService): RedirectResponse
    {
        $notificationService->markAsRead(request()->user(), $notificationId);

        return redirect()->back();
    }

    public function readAll(NotificationService $notificationService): RedirectResponse
    {
        $notificationService->markAllAsRead(request()->user());

        return redirect()->back();
    }
}
