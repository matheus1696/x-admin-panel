<?php

namespace App\Mail\Administration\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreateMail extends Mailable implements ShouldQueue
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
            subject: 'Bem-vindo ao Sistema - Seu usuário foi criado!',
        );
    }

    /**
     * Define o conteúdo do e-mail (view).
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.administration.users.user_created',
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
