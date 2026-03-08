<?php
// backend/api/game.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../models/Game.php';
require_once '../models/Player.php';
require_once '../services/DeepSeekService.php';

$method = $_SERVER['REQUEST_METHOD'];
$game = new Game();
$deepseek = new DeepSeekService();

switch($method) {
    case 'POST':
        // Создание новой игры
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch($data['action']) {
                case 'create':
                    $result = $game->create($data['host_name']);
                    echo json_encode($result);
                    break;
                    
                case 'join':
                    $result = $game->join($data['game_code'], $data['player_name']);
                    echo json_encode($result);
                    break;
                    
                case 'start':
                    // Генерация катаклизма и начало игры
                    $catastrophe = $deepseek->generateCatastrophe();
                    $bunker = $deepseek->generateBunker();
                    
                    // Сохраняем в БД
                    // ... код сохранения
                    
                    echo json_encode([
                        'catastrophe' => $catastrophe,
                        'bunker' => $bunker
                    ]);
                    break;
                    
                case 'end_game':
                    $ending = $deepseek->generateEnding($data['game_data']);
                    echo json_encode(['ending' => $ending]);
                    break;
            }
        }
        break;
        
    case 'GET':
        if (isset($_GET['game_id'])) {
            $state = $game->getGameState($_GET['game_id']);
            $players = $game->getPlayers($_GET['game_id']);
            
            echo json_encode([
                'state' => $state,
                'players' => $players
            ]);
        }
        break;
}
?>