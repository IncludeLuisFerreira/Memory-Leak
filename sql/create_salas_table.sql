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
