<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\GameMatch;

interface GameMatchRepositoryInterface
{
    public function save(GameMatch $gameMatch): void;
    public function findByUserId(int $userId): array;
    public function getRanking(string $criteria): array;
}
