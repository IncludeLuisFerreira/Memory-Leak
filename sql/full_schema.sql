CREATE DATABASE IF NOT EXISTS MemoryLeak;
USE MemoryLeak;

CREATE TABLE Usuarios (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	senha_hash VARCHAR(255) NOT NULL
);

CREATE TABLE Partidas (
	id INT AUTO_INCREMENT PRIMARY KEY,
	usuario_id INT NOT NULL,
	data DATETIME DEFAULT CURRENT_TIMESTAMP,
	tempo INT NOT NULL DEFAULT 0,
	modo ENUM('1','2') NOT NULL,
	vencedor TINYINT DEFAULT 0,
	pontos INT,
	FOREIGN KEY (usuario_id) REFERENCES Usuarios(id)
);

CREATE TABLE Ranking (
	usuario_id INT PRIMARY KEY,
	total_partidas INT DEFAULT 0,
	vitorias INT DEFAULT 0,
	tempo_medio FLOAT DEFAULT 0,
	FOREIGN KEY (usuario_id) REFERENCES Usuarios(id)
);

CREATE TABLE Salas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jogador1_id INT NOT NULL,
    jogador2_id INT DEFAULT NULL,
    estado_tabuleiro JSON NOT NULL,
    turno INT NOT NULL,
    status ENUM('aguardando', 'jogando', 'finalizada') DEFAULT 'aguardando',
    criado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (jogador1_id) REFERENCES Usuarios(id),
    FOREIGN KEY (jogador2_id) REFERENCES Usuarios(id),
    FOREIGN KEY (turno) REFERENCES Usuarios(id)
);
