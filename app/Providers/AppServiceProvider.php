<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define o estilo padrão de paginação
        Paginator::defaultView('components.pagination');

        // Define o estilo padrão de paginação
        VerifyEmail::toMailUsing(function ($notifiable, $url) {

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Verifique sua conta')
            ->view('emails.administration.users.user_verification', [
                'user' => $notifiable,
                'verificationUrl' => $url,
            ]);
    });
    }
}
