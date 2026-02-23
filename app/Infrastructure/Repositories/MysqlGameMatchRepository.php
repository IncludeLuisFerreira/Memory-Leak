<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\GameMatch;
use App\Domain\Repositories\GameMatchRepositoryInterface;
use PDO;

class MysqlGameMatchRepository implements GameMatchRepositoryInterface
{
    public function __construct(private PDO $db) {}

    public function save(GameMatch $gameMatch): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT INTO Partidas (usuario_id, tempo, modo, vencedor, pontos) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $gameMatch->getUserId(),
                $gameMatch->getTime(),
                $gameMatch->getMode(),
                $gameMatch->getWinner(),
                $gameMatch->getPoints()
            ]);
            $gameMatch->setId((int)$this->db->lastInsertId());

            // Update Ranking table
            $sqlRanking = "INSERT INTO Ranking (usuario_id, total_partidas, vitorias, tempo_medio)
                           VALUES (?, 1, ?, ?)
                           ON DUPLICATE KEY UPDATE
                           total_partidas = total_partidas + 1,
                           vitorias = vitorias + ?,
                           tempo_medio = ((tempo_medio * (total_partidas - 1)) + ?) / total_partidas";

            $vitoria = ($gameMatch->getWinner() == 1) ? 1 : 0;
            $stmtRanking = $this->db->prepare($sqlRanking);
            $stmtRanking->execute([
                $gameMatch->getUserId(),
                $vitoria,
                (float)$gameMatch->getTime(),
                $vitoria,
                (float)$gameMatch->getTime()
            ]);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM Partidas WHERE usuario_id = ? ORDER BY data DESC");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        return array_map(fn($row) => $this->mapToEntity($row), $rows);
    }

    public function getRanking(string $criteria): array
    {
        $sql = "SELECT u.nome, r.total_partidas, r.vitorias, r.tempo_medio
                FROM Ranking r
                JOIN Usuarios u ON u.id = r.usuario_id
                ORDER BY r.vitorias DESC, r.tempo_medio ASC
                LIMIT 10";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    private function mapToEntity(array $row): GameMatch
    {
        return new GameMatch(
            (int)$row['id'],
            (int)$row['usuario_id'],
            (int)$row['tempo'],
            $row['modo'],
            (int)$row['vencedor'],
            (int)$row['pontos'],
            new \DateTime($row['data'] ?? 'now')
        );
    }
}
