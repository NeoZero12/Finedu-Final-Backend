<?php

namespace App\Providers;

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
        config([
            'mail.mailers.smtp.scheme' => ((int) config('mail.mailers.smtp.port', 2525) === 465) ? 'smtps' : 'smtp',
        ]);
    }
}
