<?php

namespace App\Services\Notification;

use App\Models\Administration\User\User;
use App\Notifications\SystemNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * @param  \App\Models\Administration\User\User|\Illuminate\Support\Collection<int, \App\Models\Administration\User\User>|array<int, \App\Models\Administration\User\User>  $recipients
     * @param  array{
     *     url?:string|null,
     *     icon?:string|null,
     *     level?:string|null,
     *     send_email?:bool,
     *     mail_subject?:string|null,
     *     mail_action_text?:string|null,
     *     meta?:array<string, mixed>
     * }  $options
     */
    public function send(User|Collection|array $recipients, string $title, string $message, array $options = []): void
    {
        $users = $this->normalizeRecipients($recipients);

        if ($users->isEmpty()) {
            return;
        }

        $notification = new SystemNotification([
            'title' => $title,
            'message' => $message,
            'url' => $options['url'] ?? null,
            'icon' => $options['icon'] ?? 'fa-regular fa-bell',
            'level' => $options['level'] ?? 'info',
            'send_email' => (bool) ($options['send_email'] ?? false),
            'mail_subject' => $options['mail_subject'] ?? null,
            'mail_action_text' => $options['mail_action_text'] ?? null,
            'meta' => $options['meta'] ?? [],
        ]);

        $users->each(fn (User $user) => $user->notify($notification));
    }

    /**
     * @return array{
     *     unread_count:int,
     *     recent:\Illuminate\Support\Collection<int, array<string, mixed>>
     * }
     */
    public function summaryForUser(User $user, int $limit = 5): array
    {
        $recent = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();

        return [
            'unread_count' => $user->unreadNotifications()->count(),
            'recent' => $recent->map(fn (DatabaseNotification $notification) => $this->mapNotification($notification)),
        ];
    }

    public function paginateForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()
            ->latest()
            ->paginate($perPage)
            ->through(fn (DatabaseNotification $notification) => $this->mapNotification($notification));
    }

    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()
            ->whereKey($notificationId)
            ->first();

        if (! $notification) {
            return false;
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return true;
    }

    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * @param  \App\Models\Administration\User\User|\Illuminate\Support\Collection<int, \App\Models\Administration\User\User>|array<int, \App\Models\Administration\User\User>  $recipients
     * @return \Illuminate\Support\Collection<int, \App\Models\Administration\User\User>
     */
    private function normalizeRecipients(User|Collection|array $recipients): Collection
    {
        if ($recipients instanceof User) {
            return collect([$recipients]);
        }

        if ($recipients instanceof EloquentCollection) {
            return $recipients
                ->filter(fn ($recipient) => $recipient instanceof User)
                ->values();
        }

        return collect($recipients)
            ->filter(fn ($recipient) => $recipient instanceof User)
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function mapNotification(DatabaseNotification $notification): array
    {
        $data = $notification->data;

        return [
            'id' => $notification->id,
            'title' => $data['title'] ?? 'Notificacao',
            'message' => $data['message'] ?? '',
            'url' => $data['url'] ?? null,
            'icon' => $data['icon'] ?? 'fa-regular fa-bell',
            'level' => $data['level'] ?? 'info',
            'is_read' => $notification->read_at !== null,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
            'meta' => $data['meta'] ?? [],
        ];
    }
}
