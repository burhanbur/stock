<?php 

namespace App\Utilities;

class TelegramNotification 
{
    /**
     * Send a message to Telegram.
     * This is a static helper so callers can use TelegramNotification::sendMessage(...).
     * @param string $message
     * @return array
     */
    public static function sendMessage($message = '')
    {
        $botToken = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');
        $parseMode = config('telegram.parse_mode');
        $disableNotification = config('telegram.disable_notification');
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $body = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => $parseMode,
            'disable_notification' => $disableNotification,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }
}