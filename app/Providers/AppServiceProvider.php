<?php

namespace App\Providers;

use App\Console\Commands\CatalogCommand;
use App\Console\Commands\StartCommand;
use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Api;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Api::class, function () {
            return new Api(env('TELEGRAM_BOT_TOKEN'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(Api::class)->addCommands([
            StartCommand::class,
            CatalogCommand::class,
        ]);
    }
}
