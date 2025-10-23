<?php

namespace App\Mail\Admin\Manage;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    /**
     * Cria uma nova instância do e-mail.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Define o assunto do e-mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua senha foi redefinida',
        );
    }

    /**
     * Define o conteúdo do e-mail (markdown).
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.manage.users.user_reset_password',
            with: [
                'user' => $this->user,
                'password' => $this->password,
            ]
        );
    }

    /**
     * Define anexos (se houver).
     */
    public function attachments(): array
    {
        return [];
    }
}
