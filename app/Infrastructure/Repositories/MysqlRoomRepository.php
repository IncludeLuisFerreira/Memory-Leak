<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Room;
use App\Domain\Repositories\RoomRepositoryInterface;
use PDO;

class MysqlRoomRepository implements RoomRepositoryInterface
{
    public function __construct(private PDO $db) {}

    public function findById(int $id): ?Room
    {
        $stmt = $this->db->prepare("SELECT * FROM Salas WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->mapToEntity($row);
    }

    public function findAvailable(): ?Room
    {
        $stmt = $this->db->prepare("SELECT * FROM Salas WHERE status = 'aguardando' AND jogador2_id IS NULL LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->mapToEntity($row);
    }

    public function save(Room $room): void
    {
        if ($room->getId()) {
            $this->update($room);
        } else {
            $stmt = $this->db->prepare("INSERT INTO Salas (jogador1_id, jogador2_id, estado_tabuleiro, turno, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $room->getPlayer1Id(),
                $room->getPlayer2Id(),
                json_encode($room->getBoardState()),
                $room->getTurnUserId(),
                $room->getStatus()
            ]);
            $room->setId((int)$this->db->lastInsertId());
        }
    }

    public function update(Room $room): void
    {
        $stmt = $this->db->prepare("UPDATE Salas SET jogador1_id = ?, jogador2_id = ?, estado_tabuleiro = ?, turno = ?, status = ? WHERE id = ?");
        $stmt->execute([
            $room->getPlayer1Id(),
            $room->getPlayer2Id(),
            json_encode($room->getBoardState()),
            $room->getTurnUserId(),
            $room->getStatus(),
            $room->getId()
        ]);
    }

    private function mapToEntity(array $row): Room
    {
        return new Room(
            (int)$row['id'],
            (int)$row['jogador1_id'],
            $row['jogador2_id'] ? (int)$row['jogador2_id'] : null,
            json_decode($row['estado_tabuleiro'], true),
            (int)$row['turno'],
            $row['status'],
            new \DateTime($row['criado_at'] ?? 'now'),
            new \DateTime($row['atualizado_at'] ?? 'now')
        );
    }
}
