<?php

namespace App\Handlers;

use App\Models\Product;

class TextAnalyzer
{
    protected $intents = [
        'buy' => ['buy', 'purchase', 'want', 'get', 'order'],
        'sell' => ['sell', 'have', 'stock', 'available', 'carry']
    ];

    public function analyze($text)
    {
        $text = strtolower(trim($text));
        $products = Product::all()->pluck('name')->map('strtolower')->unique()->toArray();

        $intent = $this->detectIntent($text);
        $productName = $this->detectProduct($text, $products);
        $negated = str_contains($text, 'not') || str_contains($text, 'dont') || str_contains($text, "don't");

        // Flip intent if negated
        if ($negated && $intent === 'buy') {
            $intent = null; // Or a "not_buy" intent if you want to handle it
        }

        return [
            'intent' => $intent,
            'product' => $productName ? ucfirst($productName) : null,
            'raw_text' => $text,
            'negated' => $negated
        ];
    }

    protected function detectIntent($text)
    {
        // Check for keywords
        foreach ($this->intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $intent;
                }
            }
        }
        return null;
    }

    protected function detectProduct($text, $products)
    {
        foreach ($products as $product) {
            // Exact match
            if (str_contains($text, $product)) {
                return $product;
            }
            // Fuzzy match (e.g., "drss" -> "dress")
            $words = explode(' ', $text);
            foreach ($words as $word) {
                if (strlen($word) > 2 && similar_text($word, $product, $percent) && $percent > 80) {
                    return $product;
                }
            }
        }
        return null;
    }
}
