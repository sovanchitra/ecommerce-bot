<?php

namespace App\Handlers;

use Telegram\Bot\Api;
use App\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;

class ActionHandler
{
    public function handle(Api $telegram, $chatId, $analysis)
    {
        $intent = $analysis['intent'];
        $productName = $analysis['product'];
        $negated = $analysis['negated'];

        if (!$intent && !$productName) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Sorry, I didn’t understand. Try 'I want to buy a dress' or /catalog."
            ]);
            return;
        }

        if ($intent === 'buy' && $productName) {
            $this->handleBuy($telegram, $chatId, $productName);
        } elseif ($intent === 'sell' && $productName) {
            $this->handleSell($telegram, $chatId, $productName);
        } elseif ($productName && !$intent) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Did you mean to buy {$productName} or check if we sell it? Try 'I want to buy a {$productName}' or 'Do you sell {$productName}?'"
            ]);
        } elseif ($negated) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Okay, I won’t show you anything you don’t want! Try /catalog to see what’s available."
            ]);
        } else {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "I got part of that! Do you want to buy or check availability? Try /catalog."
            ]);
        }
    }

    protected function handleBuy(Api $telegram, $chatId, $productName)
    {
        $matches = Product::where('name', $productName)->get();
        if ($matches->isEmpty()) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Sorry, no {$productName}s in stock."
            ]);
            return;
        }

        $message = "Here are the {$productName}s:\n";
        foreach ($matches as $index => $product) {
            $message .= ($index + 1) . ". {$product->name} - $" . number_format($product->price, 2) . " - Stock: {$product->stock}\n";
        }

        $keyboard = Keyboard::make()->inline();
        foreach ($matches as $product) {
            $keyboard->row([
                Keyboard::inlineButton(['text' => "View {$product->name}", 'callback_data' => "view_product_{$product->id}"])
            ]);
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
            'reply_markup' => $keyboard
        ]);
    }

    protected function handleSell(Api $telegram, $chatId, $productName)
    {
        $inStock = Product::where('name', $productName)->exists();
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $inStock ? "Yes, we sell {$productName}s! Try 'I want to buy a {$productName}'." : "No, we don’t sell {$productName}s."
        ]);
    }
}
