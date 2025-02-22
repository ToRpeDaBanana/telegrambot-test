<?php

class Database {
    private $pdo;

    public function __construct($host, $db, $user, $pass) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }

        // Проверяем наличие таблицы users, если её нет, создаём
        $this->createUsersTable();
    }

    private function createUsersTable() {
        $query = "
        CREATE TABLE IF NOT EXISTS users (
            id BIGINT PRIMARY KEY,
            balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
        ";

        // Выполняем запрос на создание таблицы
        $this->pdo->exec($query);
    }

    // Метод для получения баланса пользователя
    public function getUserBalance($userId) {
        $stmt = $this->pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    // Метод для добавления нового пользователя
    public function addUser($userId) {
        $stmt = $this->pdo->prepare("INSERT INTO users (id, balance) VALUES (?, 0.00)");
        $stmt->execute([$userId]);
    }

    // Метод для обновления баланса пользователя
    public function updateUserBalance($userId, $balance) {
        $stmt = $this->pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$balance, $userId]);
    }
}
