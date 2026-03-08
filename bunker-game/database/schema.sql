-- database/schema.sql
CREATE DATABASE bunker_game;
USE bunker_game;

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_code VARCHAR(6) UNIQUE NOT NULL,
    status ENUM('lobby', 'character_selection', 'discussion', 'voting', 'finished') DEFAULT 'lobby',
    catastrophe TEXT,
    ending_story TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    is_host BOOLEAN DEFAULT FALSE,
    is_alive BOOLEAN DEFAULT TRUE,
    character_data JSON, -- Храним всю карточку персонажа как JSON
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

CREATE TABLE bunkers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT UNIQUE NOT NULL,
    size INT,
    supplies TEXT,
    weapon TEXT,
    description TEXT,
    special_features TEXT,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    voter_id INT NOT NULL,
    target_id INT NOT NULL,
    round INT DEFAULT 1,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (voter_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (target_id) REFERENCES players(id) ON DELETE CASCADE
);