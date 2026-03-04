<?php

namespace App\Support\Notifications;

use App\Models\Administration\User\User;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Collection;

trait InteractsWithSystemNotifications
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
    protected function notifyUsers(User|Collection|array $recipients, string $title, string $message, array $options = []): void
    {
        app(NotificationService::class)->send($recipients, $title, $message, $options);
    }
}
