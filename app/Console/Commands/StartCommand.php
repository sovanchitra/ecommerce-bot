<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start the Clothing Bot';

    public function handle()
    {
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();
        Log::info('Start command triggered for chat ID: ' . $chatId);

        $keyboard = Keyboard::make()->inline()
            ->row([Keyboard::inlineButton([
                'text' => 'Open MiniApp',
                'web_app' => ['url' => env('WEB_APP_URL')]
            ])]);

        $this->replyWithMessage([
            'text' => 'Welcome to the Clothing Bot!',
            'reply_markup' => $keyboard
        ]);
    }
}
