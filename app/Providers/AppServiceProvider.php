<?php

namespace App\Providers;

use App\Services\Notification\NotificationService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        Paginator::defaultView('components.pagination');

        View::composer('layouts.app', function ($view): void {
            $summary = [
                'unread_count' => 0,
                'recent' => collect(),
            ];

            if (Auth::check()) {
                $summary = app(NotificationService::class)->summaryForUser(Auth::user());
            }

            $view->with('layoutNotificationSummary', $summary);
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifique sua conta')
                ->view('emails.administration.users.user_verification', [
                    'user' => $notifiable,
                    'verificationUrl' => $url,
                ]);
        });
    }
}
