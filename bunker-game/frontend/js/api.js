// frontend/js/api.js
const API = {
    baseUrl: 'http://localhost:8000/backend/api',
    
    async request(endpoint, method = 'GET', data = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json'
            }
        };
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(`${this.baseUrl}/${endpoint}`, options);
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },
    
    // Создание игры
    createGame(hostName) {
        return this.request('game.php', 'POST', {
            action: 'create',
            host_name: hostName
        });
    },
    
    // Присоединение к игре
    joinGame(gameCode, playerName) {
        return this.request('game.php', 'POST', {
            action: 'join',
            game_code: gameCode,
            player_name: playerName
        });
    },
    
    // Получение состояния игры
    getGameState(gameId) {
        return this.request(`game.php?game_id=${gameId}`);
    },
    
    // Старт игры
    startGame(gameId) {
        return this.request('game.php', 'POST', {
            action: 'start',
            game_id: gameId
        });
    },
    
    // Генерация персонажа
    async generateCharacter() {
        return this.request('deepseek.php', 'POST', {
            action: 'generate_character'
        });
    },
    
    // Завершение игры
    endGame(gameId, gameData) {
        return this.request('game.php', 'POST', {
            action: 'end_game',
            game_id: gameId,
            game_data: gameData
        });
    }
};

// Хранение состояния игры
const GameState = {
    gameId: null,
    playerId: null,
    gameCode: null,
    players: [],
    status: null,
    
    saveGame(data) {
        this.gameId = data.game_id;
        this.playerId = data.player_id;
        this.gameCode = data.game_code;
        localStorage.setItem('bunker_game', JSON.stringify({
            gameId: this.gameId,
            playerId: this.playerId,
            gameCode: this.gameCode
        }));
    },
    
    loadGame() {
        const saved = localStorage.getItem('bunker_game');
        if (saved) {
            const data = JSON.parse(saved);
            this.gameId = data.gameId;
            this.playerId = data.playerId;
            this.gameCode = data.gameCode;
            return true;
        }
        return false;
    },
    
    clear() {
        localStorage.removeItem('bunker_game');
        this.gameId = null;
        this.playerId = null;
        this.gameCode = null;
    }
};