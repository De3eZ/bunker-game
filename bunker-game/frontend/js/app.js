// frontend/js/app.js
document.addEventListener('DOMContentLoaded', () => {
    // Проверяем есть ли активная игра
    if (GameState.loadGame()) {
        window.location.href = `lobby.html?game=${GameState.gameCode}`;
    }
    
    // Обработчик создания игры
    const createForm = document.getElementById('createGameForm');
    if (createForm) {
        createForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const hostName = document.getElementById('hostName').value;
            
            try {
                const result = await API.createGame(hostName);
                
                if (!result.error) {
                    GameState.saveGame(result);
                    window.location.href = `lobby.html?game=${result.game_code}`;
                } else {
                    alert('Ошибка: ' + result.error);
                }
            } catch (error) {
                alert('Ошибка соединения с сервером');
            }
        });
    }
    
    // Обработчик присоединения
    const joinForm = document.getElementById('joinGameForm');
    if (joinForm) {
        joinForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const playerName = document.getElementById('playerName').value;
            const gameCode = document.getElementById('gameCode').value.toUpperCase();
            
            try {
                const result = await API.joinGame(gameCode, playerName);
                
                if (!result.error) {
                    GameState.saveGame(result);
                    window.location.href = `lobby.html?game=${gameCode}`;
                } else {
                    alert('Ошибка: ' + result.error);
                }
            } catch (error) {
                alert('Ошибка соединения с сервером');
            }
        });
    }
});

// Утилиты
function showModal(title, content) {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    modalTitle.textContent = title;
    modalBody.innerHTML = content;
    modal.style.display = 'block';
}

function hideModal() {
    document.getElementById('modal').style.display = 'none';
}

// Закрытие модального окна по клику вне его
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        hideModal();
    }
}