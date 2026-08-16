<?php 

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
    'chat_id' => env('TELEGRAM_CHAT_ID', ''),
    'parse_mode' => env('TELEGRAM_PARSE_MODE', 'html'),
    'disable_notification' => env('TELEGRAM_DISABLED_NOTIFICATION', true),
];