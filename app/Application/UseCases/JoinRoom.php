<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\RoomRepositoryInterface;
use App\Domain\Entities\Room;

class JoinRoom
{
    public function __construct(private RoomRepositoryInterface $roomRepository) {}

    public function execute(int $userId): Room
    {
        $room = $this->roomRepository->findAvailable();

        if ($room) {
            if ($room->getPlayer1Id() !== $userId) {
                $room->setPlayer2Id($userId);
                $room->setStatus(Room::STATUS_PLAYING);
                $this->roomRepository->update($room);
            }
            return $room;
        }

        // Create new room
        $boardState = $this->generateBoard();
        $newRoom = new Room(
            null,
            $userId,
            null,
            $boardState,
            $userId,
            Room::STATUS_WAITING
        );
        $this->roomRepository->save($newRoom);
        return $newRoom;
    }

    private function generateBoard(): array
    {
        $images = [1, 2, 3, 4, 5, 6, 7, 8];
        $cards = array_merge($images, $images);
        shuffle($cards);

        $board = [];
        foreach ($cards as $id) {
            $board[] = [
                'id' => $id,
                'virada' => false,
                'par' => false
            ];
        }

        return [
            'cartas' => $board,
            'pares_jogador1' => 0,
            'pares_jogador2' => 0
        ];
    }
}
