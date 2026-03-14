<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token): string {
            $baseUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/');
            $email = $notifiable->getEmailForPasswordReset();

            return $baseUrl . '/reset-password?' . http_build_query([
                'token' => $token,
                'email' => $email,
            ]);
        });
    }
}
