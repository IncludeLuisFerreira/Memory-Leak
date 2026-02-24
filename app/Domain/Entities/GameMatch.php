<?php

namespace App\Domain\Entities;

class GameMatch
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private int $time,
        private string $mode,
        private int $winner,
        private int $points,
        private ?\DateTime $date = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getTime(): int { return $this->time; }
    public function getMode(): string { return $this->mode; }
    public function getWinner(): int { return $this->winner; }
    public function getPoints(): int { return $this->points; }
    public function getDate(): ?\DateTime { return $this->date; }

    public function setId(int $id): void { $this->id = $id; }
}
