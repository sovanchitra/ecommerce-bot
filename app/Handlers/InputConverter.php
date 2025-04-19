<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Message;

class InputConverter
{
    public function convert(Message $message)
    {
        if ($text = $message->getText()) {
            return $text; // Text input
        } elseif ($voice = $message->getVoice()) {
            Log::info('Received voice message', ['voice' => $voice]);
            // TODO: Convert voice to text (e.g., Google Speech-to-Text)
            return null; // Stub for now
        } elseif ($photo = $message->getPhoto()) {
            // TODO: Convert image to text (e.g., OCR) or analyze content
            return null; // Stub for now
        }
        return null;
    }
}
