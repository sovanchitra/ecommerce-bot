<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start the Clothing Bot';

    public function handle()
    {
        $chatId = $this->getUpdate()->getMessage()->getChat()->getId();
        Log::info('Start command triggered for chat ID: ' . $chatId);
        $this->replyWithMessage(['text' => 'Welcome to the Clothing Bot!']);
    }
}
