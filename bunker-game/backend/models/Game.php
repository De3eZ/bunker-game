<?php
// backend/models/Game.php
require_once __DIR__ . '/../config/database.php';

class Game {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($hostName) {
        // Генерируем уникальный код комнаты
        $gameCode = strtoupper(substr(md5(uniqid()), 0, 6));
        
        try {
            $this->db->beginTransaction();
            
            // Создаем игру
            $stmt = $this->db->prepare("
                INSERT INTO games (game_code) VALUES (?)
            ");
            $stmt->execute([$gameCode]);
            $gameId = $this->db->lastInsertId();
            
            // Создаем хоста
            $stmt = $this->db->prepare("
                INSERT INTO players (game_id, name, is_host) VALUES (?, ?, 1)
            ");
            $stmt->execute([$gameId, $hostName]);
            $playerId = $this->db->lastInsertId();
            
            $this->db->commit();
            
            return [
                'game_id' => $gameId,
                'game_code' => $gameCode,
                'player_id' => $playerId
            ];
            
        } catch(Exception $e) {
            $this->db->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    
    public function join($gameCode, $playerName) {
        // Находим игру по коду
        $stmt = $this->db->prepare("SELECT id FROM games WHERE game_code = ? AND status = 'lobby'");
        $stmt->execute([$gameCode]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$game) {
            return ['error' => 'Игра не найдена или уже началась'];
        }
        
        // Добавляем игрока
        $stmt = $this->db->prepare("
            INSERT INTO players (game_id, name) VALUES (?, ?)
        ");
        $stmt->execute([$game['id'], $playerName]);
        
        return [
            'game_id' => $game['id'],
            'player_id' => $this->db->lastInsertId()
        ];
    }
    
    public function getGameState($gameId) {
        $stmt = $this->db->prepare("
            SELECT g.*, 
                   COUNT(p.id) as players_count,
                   SUM(CASE WHEN p.is_alive = 1 THEN 1 ELSE 0 END) as alive_count
            FROM games g
            LEFT JOIN players p ON g.id = p.game_id
            WHERE g.id = ?
            GROUP BY g.id
        ");
        $stmt->execute([$gameId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getPlayers($gameId) {
        $stmt = $this->db->prepare("
            SELECT id, name, is_host, is_alive, character_data 
            FROM players 
            WHERE game_id = ?
        ");
        $stmt->execute([$gameId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>