<?php

namespace App\Console\Commands;

use App\Models\Product;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class CatalogCommand extends Command
{
    protected string $name = 'catalog';
    protected string $description = 'Browse the Clothing Bot catalog';

    public function handle()
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->replyWithMessage(['text' => 'No products available in the catalog yet.']);
            return;
        }

        // Build the message
        $message = "*Total cataloged products : " . $products->count() . "*" . "\n";
        foreach ($products as $index => $product) {
            $message .= ($index + 1) . ". " . $product->name . " - $" . number_format($product->price, 2) . " - Stock: " . $product->stock . "\n";
        }

        // Create inline keyboard
        $keyboard = Keyboard::make()->inline();
        foreach ($products as $product) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => "View " . $product->name,
                    'callback_data' => 'view_product_' . $product->id,
                ])
            ]);
        }

        // Send message with keyboard
        $this->replyWithMessage([
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard
        ]);
    }
}
