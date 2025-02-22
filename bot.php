<?php
require 'vendor/autoload.php';

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;


$token = env('TG_BOT_API_TOKEN');
$bot = new BotApi($token);

$connection = array(
    'host' => env('DB_HOST'),
    'dbname' => env('DB_NAME'),
    'user' => env('DB_USER'),
    'password' => env('DB_PASSWORD')
);