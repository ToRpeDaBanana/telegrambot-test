<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['TG_BOT_API_TOKEN'];
$webhookUrl = $_ENV['WEBHOOK_DOMAIN'];

$response = file_get_contents("https://api.telegram.org/bot$token/setWebhook?url=$webhookUrl");
echo $response;