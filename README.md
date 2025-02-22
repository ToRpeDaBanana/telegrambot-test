﻿# telegrambot-test

Этот Telegram-бот позволяет пользователям управлять своим балансом, пополняя или списывая деньги с учёта через сообщения.

## Функциональность
- При первом сообщении пользователя создаётся его учётная запись с балансом $0.00.
- Если пользователь отправляет число (положительное или отрицательное), оно изменяет баланс.
- Поддерживается ввод дробных чисел через точку и запятую.
- Баланс не может уйти в отрицательное значение.
- Работа через Long Polling (без вебхука и ngrok).

## Установка
### 1. Клонирование репозитория
```sh
git clone https://github.com/ToRpeDaBanana/telegrambot-test.git
cd telegram-bot
```

### 2. Установка зависимостей
Убедитесь, что у вас установлен PHP и Composer.
```sh
composer install
```

### 3. Создание файла конфигурации
Создайте файл `.env` в корневой папке и добавьте в него:
```
TG_BOT_API_TOKEN=your_telegram_bot_token
DB_HOST=localhost
DB_NAME=tg_bot
DB_USER=root
DB_PASSWORD=your_password
```

### 4. Настройка базы данных
Создайте базу данных `tg_bot` и выполните создание таблицы:
```sql
CREATE TABLE IF NOT EXISTS users (
    id BIGINT PRIMARY KEY,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0.00
);
```

### 5. Запуск бота
Отключите вебхук (если был установлен):
```sh
curl -X POST "https://api.telegram.org/botYOUR_BOT_TOKEN/deleteWebhook"
```

Запустите бота:
```sh
php bot.php
```

## Использование
1. Напишите боту любое число, например `10.50` — ваш баланс пополнится.
2. Напишите `-5` — сумма спишется.
3. Если списание превысит баланс, бот сообщит об ошибке.


