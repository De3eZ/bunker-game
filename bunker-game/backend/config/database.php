<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        if (!getenv('DEEPSEEK_API_KEY')) {
            // Для разработки покажем ошибку, в продакшене - залогируем
            if (getenv('APP_ENV') === 'development') {
                die('Ошибка: файл конфигурации не найден. Скопируйте env.example.php в env.php');
            } else {
                error_log('Configuration file missing');
                die('Ошибка конфигурации сервера');
            }
        }
        $host = 'localhost';
        $dbname = 'bunker_game';
        $username = 'root';
        $password = 'root';
        
        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
?>