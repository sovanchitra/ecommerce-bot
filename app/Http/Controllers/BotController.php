<?php

namespace App\Http\Controllers;

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

        return response('OK', 200);
    }

    public function setWebhook()
    {
        $url = 'https://a7ce-163-53-29-18.ngrok-free.app/telegram/webhook';
        $response = $this->telegram->setWebhook(['url' => $url]);
        return $response ? 'Webhook set successfully!' : 'Failed to set webhook.';
    }

    protected function handleCallback($callbackQuery)
    {
        $chatId = $callbackQuery->getMessage()->getChat()->getId();
        $data = $callbackQuery->getData();
        Log::info('Callback query received', ['data' => $data]);

        // Route callbacks to handlers
        if (str_starts_with($data, 'view_product_')) {
            $productId = (int) str_replace('view_product_', '', $data);
            app(ViewProductHandler::class)->handle($this->telegram, $chatId, $productId);
        }

        // Add more callback types here (e.g., 'add_to_cart_') as needed

        $this->telegram->answerCallbackQuery([
            'callback_query_id' => $callbackQuery->getId()
        ]);
    }
}
