<?php

namespace App\Handlers;

use Telegram\Bot\Api;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;

class ViewProductHandler
{
    public function handle(Api $telegram, $chatId, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            Log::warning('Product not found', ['product_id' => $productId]);
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Product not found.'
            ]);
            return;
        }

        if (!$product->image_url) {
            Log::warning('No image for product', ['product_id' => $productId]);
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "{$product->name} - $" . number_format($product->price, 2) . " - Stock: {$product->stock}\n(No image available)"
            ]);
            return;
        }

        Log::info('Sending product image', ['product_id' => $productId, 'image_url' => $product->image_url]);
        try {
            $telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => InputFile::create($product->image_url), // Works with your URL now
                'caption' => "{$product->name} - $" . number_format($product->price, 2) . " - Stock: {$product->stock}"
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send photo: ' . $e->getMessage());
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Sorry, couldn’t load the image.'
            ]);
        }
    }
}
