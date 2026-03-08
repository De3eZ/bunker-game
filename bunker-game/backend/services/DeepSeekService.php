<?php
// backend/services/DeepSeekService.php
class DeepSeekService {
    private $apiKey;
    private $apiUrl = 'https://api.deepseek.com/v1/chat/completions';
    
    public function __construct() {
        $this->apiKey = getenv('DEEPSEEK_API_KEY');
    }
    
    public function generateCatastrophe() {
        $prompt = "Ты мастер игры 'Бункер'. Сгенерируй уникальный апокалипсис. 
                   Ответ должен быть в формате JSON с полями: 
                   - name (название)
                   - description (описание)
                   - worldStatus (состояние мира)
                   - survivalRules (правила выживания)";
        
        return $this->callDeepSeek($prompt);
    }
    
    public function generateCharacter() {
        $prompt = "Сгенерируй персонажа для игры 'Бункер'. 
                   Ответ в JSON формате:
                   {
                       profession: профессия,
                       health: состояние здоровья,
                       hobby: хобби,
                       phobia: фобия,
                       knowledge: полезные знания,
                       inventory: предмет в багаже,
                       additional: дополнительная информация
                   }";
        
        return $this->callDeepSeek($prompt);
    }
    
    public function generateBunker() {
        $prompt = "Сгенерируй бункер для выживания. 
                   Ответ JSON:
                   {
                       size: вместимость (число),
                       supplies: какие запасы есть,
                       weapon: оружие,
                       description: описание,
                       special: особенности
                   }";
        
        return $this->callDeepSeek($prompt);
    }
    
    public function generateEnding($gameData) {
        $players = json_encode($gameData['players']);
        $bunker = json_encode($gameData['bunker']);
        $catastrophe = $gameData['catastrophe'];
        
        $prompt = "Игра 'Бункер' завершена. 
                   Катаклизм: $catastrophe
                   Выжившие: $players
                   Бункер: $bunker
                   
                   Напиши эпическую, но короткую историю о том, как выжившие адаптируются
                   в новом мире, используя свои навыки и ресурсы бункера.
                   История должна быть драматичной и захватывающей.";
        
        return $this->callDeepSeek($prompt);
    }
    
    private function callDeepSeek($prompt) {
        $data = [
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.8
        ];
        
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['choices'][0]['message']['content'];
        }
        
        return null;
    }
}
?>