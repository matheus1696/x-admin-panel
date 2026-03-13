<?php

namespace App\Livewire\Layout;

use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsDropdown extends Component
{
    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->notificationService->markAllAsRead($user);
    }

    public function render()
    {
        $user = Auth::user();

        $summary = [
            'unread_count' => 0,
            'recent' => collect(),
        ];

        if ($user) {
            $summary = $this->notificationService->summaryForUser($user);
        }

        return view('livewire.layout.notifications-dropdown', [
            'notificationSummary' => $summary,
        ]);
    }
}
