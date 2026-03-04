<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    /**
     * @param  array{
     *     title:string,
     *     message:string,
     *     url:string|null,
     *     icon:string|null,
     *     level:string|null,
     *     send_email:bool,
     *     mail_subject:string|null,
     *     mail_action_text:string|null,
     *     meta:array<string, mixed>
     * }  $payload
     */
    public function __construct(
        private readonly array $payload
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (($this->payload['send_email'] ?? false) && ! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->payload['title'],
            'message' => $this->payload['message'],
            'url' => $this->payload['url'] ?? null,
            'icon' => $this->payload['icon'] ?? 'fa-regular fa-bell',
            'level' => $this->payload['level'] ?? 'info',
            'send_email' => (bool) ($this->payload['send_email'] ?? false),
            'mail_subject' => $this->payload['mail_subject'] ?? null,
            'mail_action_text' => $this->payload['mail_action_text'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->payload['mail_subject'] ?? $this->payload['title'])
            ->greeting('Ola, '.$notifiable->name)
            ->line($this->payload['message']);

        if (! empty($this->payload['url'])) {
            $mailMessage->action(
                $this->payload['mail_action_text'] ?? 'Abrir notificacao',
                $this->payload['url']
            );
        }

        return $mailMessage->line('Esta mensagem tambem esta disponivel no painel interno.');
    }
}
