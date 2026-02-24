<?php

namespace App\Domain\Entities;

class Room
{
    public const STATUS_WAITING = 'aguardando';
    public const STATUS_PLAYING = 'jogando';
    public const STATUS_FINISHED = 'finalizada';

    public function __construct(
        private ?int $id,
        private int $player1Id,
        private ?int $player2Id,
        private array $boardState,
        private int $turnUserId,
        private string $status,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getPlayer1Id(): int { return $this->player1Id; }
    public function getPlayer2Id(): ?int { return $this->player2Id; }
    public function getBoardState(): array { return $this->boardState; }
    public function getTurnUserId(): int { return $this->turnUserId; }
    public function getStatus(): string { return $this->status; }

    public function setId(int $id): void { $this->id = $id; }
    public function setPlayer2Id(int $id): void { $this->player2Id = $id; }
    public function setBoardState(array $state): void { $this->boardState = $state; }
    public function setTurnUserId(int $id): void { $this->turnUserId = $id; }
    public function setStatus(string $status): void { $this->status = $status; }
}
