<?php

namespace App\Handlers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class TextAnalyzer
{
    public function analyze($text)
    {
        Log::info('Text to analyze', ['text' => $text]);
        $text = strtolower(trim($text));
        $products = Product::all()->pluck('name')->map('strtolower')->unique()->toArray();

        Log::info('Products', ['products' => $products]);

        $intent = $this->detectIntent($text);
        $productName = $this->detectProduct($text, $products);

        return [
            'intent' => $intent,
            'product' => $productName ? ucfirst($productName) : null,
            'raw_text' => $text
        ];
    }

    protected function detectIntent($text)
    {
        if (str_contains($text, 'buy') || str_contains($text, 'want')) {
            return 'buy';
        }
        if (str_contains($text, 'sell') || str_contains($text, 'have') || str_contains($text, 'stock')) {
            return 'sell';
        }
        return null;
    }

    protected function detectProduct($text, $products)
    {
        foreach ($products as $product) {
            if (str_contains($text, $product)) {
                return $product;
            }
        }
        return null;
    }
}
