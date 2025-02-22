<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use TelegramBot\Api\BotApi;

$token = $_ENV['TG_BOT_API_TOKEN'];
$bot = new BotApi($token);

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Создание таблицы пользователей, если её нет
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id BIGINT PRIMARY KEY,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0.00
)");

echo "Бот запущен. Ожидание сообщений...\n";

$offset = 0;
while (true) {
    try {
        $updates = $bot->getUpdates($offset);
        foreach ($updates as $update) {
            $message = $update->getMessage();
            if (!$message) continue;

            $userId = $message->getFrom()->getId();
            $text = str_replace(',', '.', trim($message->getText())); // Поддержка ',' и '.'

            // Проверяем, есть ли пользователь в БД
            $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                // Добавляем нового пользователя
                $pdo->prepare("INSERT INTO users (id, balance) VALUES (?, 0.00)")->execute([$userId]);
                $balance = 0.00;
            } else {
                $balance = $user['balance'];
            }

            // Если сообщение — число, изменяем баланс
            if (is_numeric($text)) {
                $amount = floatval($text);
                $newBalance = $balance + $amount;

                if ($newBalance < 0) {
                    $bot->sendMessage($userId, "Ошибка: недостаточно средств. Ваш баланс: " . $balance . "$");
                } else {
                    $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?")->execute([$newBalance, $userId]);
                    $bot->sendMessage($userId, "Ваш баланс: " . $newBalance . "$");
                }
            } else {
                $bot->sendMessage($userId, "Отправьте сумму для пополнения или списания (например: 10.50 или -5)");
            }

            $offset = $update->getUpdateId() + 1;
        }
    } catch (Exception $e) {
        echo "Ошибка: " . $e->getMessage() . "\n";
    }

    sleep(1); // Ожидание 1 секунда перед следующим запросом
}
