<?php

namespace App\Mail\Profile;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserPasswordResetedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    /**
     * Cria uma nova instância do e-mail.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Define o assunto do e-mail.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Você redefiniu sua senha',
        );
    }

    /**
     * Define o conteúdo do e-mail (view).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.profile.user_password_reseted',
            with: [
                'user' => $this->user,
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
