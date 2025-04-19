<?php

namespace App\Http\Controllers;

use App\Handlers\ActionHandler;
use App\Handlers\InputConverter;
use App\Handlers\TextAnalyzer;
use App\Handlers\ViewProductHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class BotController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle(Request $request)
    {
        Log::info('Webhook update:', $request->all());

        $update = $this->telegram->commandsHandler(true);

        if (!$update) {
            Log::warning('No update received', $request->all());
            return response('OK', 200);
        }

        // Handle callback queries
        if ($callbackQuery = $update->getCallbackQuery()) {
            $this->handleCallback($callbackQuery);
        }

        // Handle all input types (text, voice, image)
        if ($message = $update->getMessage()) {
            $chatId = $message->getChat()->getId();
            $this->handleInput($message, $chatId);
        }

        return response('OK', 200);
    }

    protected function handleCallback($callbackQuery)
    {
        $chatId = $callbackQuery->getMessage()->getChat()->getId();
        $data = $callbackQuery->getData();
        Log::info('Callback query received', ['data' => $data]);

        // Handle view product callback
        if (str_starts_with($data, 'view_product_')) {
            $productId = (int) str_replace('view_product_', '', $data);
            app(ViewProductHandler::class)->handle($this->telegram, $chatId, $productId);
        }

        $this->telegram->answerCallbackQuery(['callback_query_id' => $callbackQuery->getId()]);
    }

    protected function handleInput($message, $chatId)
    {
        $converter = app(InputConverter::class);
        $text = $converter->convert($message); // Converts input to text

        // Skip commands
        if ($text && !str_starts_with($text, '/')) {
            Log::info('Input converted to text', ['text' => $text]);
            $analyzer = app(TextAnalyzer::class);
            $analysis = $analyzer->analyze($text);

            $handler = app(ActionHandler::class);
            $handler->handle($this->telegram, $chatId, $analysis);
        }
    }

    public function setWebhook()
    {
        $url = 'https://man-big-needlessly.ngrok-free.app/telegram/webhook';
        $response = $this->telegram->setWebhook(['url' => $url]);
        return $response ? 'Webhook set successfully!' : 'Failed to set webhook.';
    }
}
