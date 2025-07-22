CREATE DATABASE MemoryLeak;
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

DELIMITER $$

CREATE PROCEDURE get_ranking_agrupado(IN criterio VARCHAR(10))
BEGIN
    DECLARE ordem_sql VARCHAR(1000);
    DECLARE stmt_query VARCHAR(2000);

    -- Define a cláusula de ordenação com base no critério
    IF criterio = 'tempo' THEN
        SET ordem_sql = ' ORDER BY melhor_tempo ASC';
    ELSEIF criterio = 'vitorias' THEN
        SET ordem_sql = ' ORDER BY partidas_ganhas DESC';
    ELSE
        SET ordem_sql = ' ORDER BY total_pontos DESC';
    END IF;

    -- Monta a consulta dinâmica
    SET @stmt_query = CONCAT(
        'SELECT u.nome, ',
        'SUM(p.pontos) AS total_pontos, ',
        'MIN(p.tempo) AS melhor_tempo, ',
        'COUNT(*) AS partidas_ganhas ',
        'FROM Partidas p ',
        'JOIN Usuarios u ON p.usuario_id = u.id ',
        'WHERE p.vencedor = 1 ',
        'GROUP BY u.id, u.nome',
        ordem_sql,
        ' LIMIT 20'
    );

    -- Executa a consulta dinâmica
    PREPARE stmt FROM @stmt_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$


create procedure get_history(email varchar(100))
begin
	select p.pontos, p.tempo, p.vencedor, p.vencedor
    from Partidas p
    join Usuarios u on u.id = p.usuario_id
    where u.email = email;
end$$

DELIMITER ;

call get_history(3);
CALL get_ranking_agrupado('pontos');
